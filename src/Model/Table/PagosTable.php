<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pagos
 *
 * @method \App\Model\Entity\Pago newEmptyEntity()
 * @method \App\Model\Entity\Pago get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Pago patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pago|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PagosTable extends Table
{
    /**
     * @param array<string, mixed> $config Configuración
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pagos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'created' => 'fecha_creacion',
            'modified' => 'fecha_actualizacion',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * @return list<string>
     */
    public static function metodos(): array
    {
        return [
            'tarjeta_credito',
            'tarjeta_debito',
            'paypal',
            'transferencia',
            'efectivo',
            'cripto',
        ];
    }

    /**
     * @return list<string>
     */
    public static function estados(): array
    {
        return ['pendiente', 'completado', 'fallido', 'reembolsado', 'cancelado'];
    }

    /**
     * @param \Cake\Validation\Validator $validator Validador
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->scalar('metodo')
            ->inList('metodo', self::metodos())
            ->requirePresence('metodo', 'create')
            ->notEmptyString('metodo');

        $validator
            ->decimal('monto')
            ->greaterThanOrEqual('monto', 0)
            ->requirePresence('monto', 'create')
            ->notEmptyString('monto');

        $validator
            ->scalar('estado')
            ->inList('estado', self::estados())
            ->notEmptyString('estado');

        $validator
            ->scalar('descripcion')
            ->allowEmptyString('descripcion');

        $validator
            ->dateTime('fecha_pago')
            ->allowEmptyDateTime('fecha_pago');

        return $validator;
    }
}
