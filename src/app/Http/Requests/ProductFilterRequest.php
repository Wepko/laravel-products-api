<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\Product\ProductFilterDTO;
use App\Enums\ProductSortEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'price_from' => ['nullable', 'numeric', 'min:0',],
            'price_to' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'], // предполагается, что есть таблица categorie],
            'in_stock' => ['nullable', 'boolean'],
            'rating_from' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort' => ['nullable', 'string', Rule::in(ProductSortEnum::values())],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'cursor' => ['nullable', 'string']
        ];
    }

    public function toDTO(): ProductFilterDTO
    {
        return ProductFilterDTO::validateAndCreate($this->validated());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'price_from.numeric' => 'Минимальная цена должна быть числом',
            'price_from.min' => 'Минимальная цена не может быть отрицательной',
            'price_to.numeric' => 'Максимальная цена должна быть числом',
            'price_to.min' => 'Максимальная цена не может быть отрицательной',
            'category_id.exists' => 'Указанная категория не существует',
            'rating_from.min' => 'Рейтинг не может быть меньше 0',
            'rating_from.max' => 'Рейтинг не может быть больше 5',
            'sort.in' => 'Недопустимый параметр сортировки',
            'page.min' => 'Номер страницы должен быть не менее 1',
            'per_page.min' => 'Количество товаров на странице должно быть не менее 1',
            'per_page.max' => 'Количество товаров на странице не может превышать 100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'q' => 'поисковый запрос',
            'price_from' => 'минимальная цена',
            'price_to' => 'максимальная цена',
            'category_id' => 'ID категории',
            'in_stock' => 'наличие на складе',
            'rating_from' => 'минимальный рейтинг',
            'sort' => 'сортировка',
            'page' => 'страница',
            'per_page' => 'количество на странице',
            'cursor' => 'курсор',
        ];
    }

    /**
     * Дополнительная валидация после основных правил.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Проверка, что price_from не больше price_to
            if ($this->price_from && $this->price_to && $this->price_from > $this->price_to) {
                $validator->errors()->add(
                    'price_from',
                    'Минимальная цена не может быть больше максимальной'
                );
            }

            // Проверка на конфликт пагинации (page и cursor)
            if ($this->page > 1 && $this->cursor) {
                $validator->errors()->add(
                    'cursor',
                    'Нельзя использовать одновременно пагинацию по страницам и курсорную пагинацию'
                );
            }
        });
    }
}
