<?php

declare(strict_types=1);

namespace Crmleaf\Payroll\Tools\TdsCalculator\Http\Requests;

use Crmleaf\Payroll\Money;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the wire input for TDS Calculator and turns it into named arguments
 * for Crmleaf\Payroll\Calculators\TdsCalculator::calculate().
 *
 * Optional fields that were not sent are left out of the payload entirely
 * rather than passed as null, so the calculator's own documented defaults apply
 * and there is exactly one place each default is written down.
 */
final class TdsCalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        if (!$this->submitted()) {
            return [];
        }

        return [
            'monthly_gross' => ['required', 'numeric', 'min:0'],
            'regime' => ['nullable', 'string', 'in:new,old'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'deductions' => ['nullable', 'array'],
            'months_remaining' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tax_already_deducted' => ['nullable', 'numeric', 'min:0'],
            'as_of' => ['nullable', 'date'],
        ];
    }

    /**
     * Named arguments for TdsCalculator::calculate().
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        /** @var array<string, mixed> $input */
        $input = $this->validated();

        $payload = [
            'monthlyGross' => Money::fromRupees((float) $input['monthly_gross']),
        ];

        if (array_key_exists('regime', $input) && $input['regime'] !== null) {
            $payload['regime'] = (string) $input['regime'];
        }

        if (array_key_exists('age', $input) && $input['age'] !== null) {
            $payload['age'] = (int) $input['age'];
        }

        if (array_key_exists('deductions', $input) && $input['deductions'] !== null) {
            $payload['deductions'] = (array) $input['deductions'];
        }

        if (array_key_exists('months_remaining', $input) && $input['months_remaining'] !== null) {
            $payload['monthsRemaining'] = (int) $input['months_remaining'];
        }

        if (array_key_exists('tax_already_deducted', $input) && $input['tax_already_deducted'] !== null) {
            $payload['taxAlreadyDeducted'] = Money::fromRupees((float) $input['tax_already_deducted']);
        }

        if (array_key_exists('as_of', $input) && $input['as_of'] !== null) {
            $payload['asOf'] = new \DateTimeImmutable((string) $input['as_of']);
        }

        return $payload;
    }

    /**
     * A bare GET renders an empty form; everything else is a submission.
     */
    public function submitted(): bool
    {
        return $this->isMethod('post') || $this->expectsJson() || $this->query->count() > 0;
    }

    /**
     * The HTML form posts these as JSON text in a textarea, the JSON API sends
     * them as real arrays, and both have to reach the same validator.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'deductions' => self::decodeJson($this->input('deductions')),
        ]);
    }

    private static function decodeJson(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        $decoded = json_decode($trimmed, true);

        return is_array($decoded) ? $decoded : $value;
    }
}
