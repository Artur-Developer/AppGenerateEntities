<?php
/**
 * Created by PhpStorm.
 * User: vipma
 * Date: 14.04.2020
 * Time: 16:06
 */

namespace backend\models;

use Yii;

class Generator
{
    const DEFAULT_CNT_IN_BATCH = 500;
    const INTERVAL_SAVE_BATCH = 1;

    public $cnt_batches = 0;
    public $inserted = 0;

    protected $entity;
    protected $colors;
    protected $state;
    protected $columns;
    protected $batch;
    protected $count;

    public function __construct(object $entity, array $columns, array $colors, int $count,  int $state)
    {
        $this->entity = $entity;
        $this->count = $count;
        $this->table_name = $this->entity->tableName();
        $this->colors = $colors;
        $this->state = $state;
        $this->columns = $columns;
        $this->cnt_batches = $this->getCntBatches();
    }

    private function generate(int $count)
    {
        $this->batch = [];
        for ($i = 1; $i <= $count; $i++){
            $this->batch[] = array_values($this->entity->buildInsert($this->colors, $this->state));
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

    public function buildBranches()
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
    }

    private function insertBatch(): int
    {
        sleep(static::INTERVAL_SAVE_BATCH);
        return Yii::$app->db->createCommand()
            ->batchInsert(
                $this->table_name, $this->columns, $this->batch
            )->execute() == 1 ?: $this->inserted += count($this->batch);

    }

    private function getCntBatches(): int
    {
        $cnt = intdiv($this->count,static::DEFAULT_CNT_IN_BATCH);
        return $cnt == 0 ? 1 : $cnt;
    }

}