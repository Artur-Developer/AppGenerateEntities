<?php

namespace backend\models;

use Yii;

class Generator
{
    const MAX_CNT_IN_BATCH = 10000; // количество в партии
    const DEFAULT_CNT_IN_BATCH = 500; // количество в партии
    const INTERVAL_SAVE_BATCH = 1; // пауза в секундах
    const STATUS_GENERATING = 20; // партия генерируется
    const STATUS_GENERATED = 21; // партия сгенерировалась

    public $cnt_batches = 0;
    public $inserted = 0;
    public $batch_id;

    protected $entity;
    protected $colors;
    protected $state;
    protected $columns;
    protected $batch;
    protected $count;

    /**
     * Generator constructor.
     * @param object $entity
     * @param array $columns
     * @param array $colors
     * @param int $count
     * @param int $state
     * @param int $batch_id
     */
    public function __construct(object $entity, array $columns, array $colors, int $count,  int $state, int $batch_id = 0)
    {
        $this->entity = $entity;
        $this->count = $count;
        $this->table_name = $this->entity->tableName();
        $this->colors = $colors;
        $this->state = $state;
        $this->columns = $columns;
        $this->cnt_batches = $this->getCntBatches();
        $this->batch_id = $batch_id == 0 ? 1 + $entity->getLastBatch() : $batch_id;
    }

    /**
     * @param int $count
     * function generate entity in db to 100 item
     */
    private function generate(int $count)
    {
        $this->batch = [];
        for ($i = 1; $i <= $count; $i++){
            $this->batch[] = array_values($this->entity->buildInsert($this->batch_id,$this->colors,$this->state));
            if ($i % 100 == 0){
                $this->insertBatch();
                unset($this->batch);
            }
            elseif ($i == $count){
                $this->insertBatch();
                unset($this->batch);
            }
        }
    }

    /**
     * @return int inserted entity in db
     * This function insert to batch
     * You can edit size batch static::DEFAULT_CNT_IN_BATCH = 500
     */
    public function buildBranches(): int
    {
        while ($this->count > 0){
            if ($this->count >= static::DEFAULT_CNT_IN_BATCH) {
                $this->generate(static::DEFAULT_CNT_IN_BATCH);
                $this->count = $this->count <= static::DEFAULT_CNT_IN_BATCH
                    ? $this->count - $this->count
                    : $this->count - static::DEFAULT_CNT_IN_BATCH;
            } else {
                $this->generate($this->count);
                $this->count -= $this->count;
            }
        }
        return $this->inserted;
    }

    /**
     * @return int - status inserted
     * 0 - error
     * 1 - ok
     */
    private function insertBatch(): int
    {
//        sleep(static::INTERVAL_SAVE_BATCH);
        return Yii::$app->db->createCommand()
            ->batchInsert(
                $this->table_name, $this->columns, $this->batch
            )->execute() == 1 ?: $this->inserted += count($this->batch);

    }

    /**
     * @return int - count batches
     * count_generate / batch_size = count batches
     */
    private function getCntBatches(): int
    {
        $cnt = intdiv($this->count,static::DEFAULT_CNT_IN_BATCH);
        return $cnt == 0 ? 1 : $cnt;
    }

}