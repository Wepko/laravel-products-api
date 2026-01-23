<?php

declare(strict_types=1);

namespace App\Services;


use Illuminate\Database\Eloquent\Builder;
use App\Support\DTOs\Pagination\PaginationRequestDTO;
use App\Support\DTOs\Pagination\PaginationResultDTO;
use App\Support\Pagination\PaginatorContext;
use App\Enums\PaginationType;

class PaginationService
{
    public function __construct(
        private PaginatorContext $paginatorContext
    ) {}

    public function paginate(Builder $query, PaginationRequestDTO $dto): PaginationResultDTO
    {
        // Устанавливаем стратегию
        $this->paginatorContext->setStrategy($dto->type);

        // Выполняем пагинацию
        return $this->paginatorContext->paginate($query, $dto);
    }

    public function paginateWithAutoStrategy(Builder $query, PaginationRequestDTO $dto): PaginationResultDTO
    {
        // Автоматически выбираем лучшую стратегию
        $optimalType = $this->detectOptimalStrategy($query, $dto);
        $dto->type = $optimalType;

        return $this->paginate($query, $dto);
    }

    private function detectOptimalStrategy(Builder $query, PaginationRequestDTO $dto): PaginationType
    {
        // Если клиент явно указал тип - используем его
        if ($dto->type !== PaginationType::AUTO) {
            return $dto->type;
        }

        // Проверяем, есть ли курсор (для бесконечного скролла)
        if (request()->has('cursor')) {
            return PaginationType::CURSOR;
        }

        // Оцениваем размер данных
        try {
            $count = $query->count();

            if ($count > 100000) {
                return PaginationType::CURSOR;
            }

            if ($count > 50000) {
                return PaginationType::SIMPLE;
            }

            return PaginationType::LENGTH_AWARE;
        } catch (\Exception $e) {
            // Если COUNT медленный, используем простую пагинацию
            return PaginationType::SIMPLE;
        }
    }
}
