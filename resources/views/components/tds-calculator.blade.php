@props([
    'action' => null,
    'method' => 'post',
    'defaults' => [],
    'input' => [],
    'result' => null,
    'error' => null,
    'heading' => 'TDS Calculator',
    'tagline' => 'Monthly TDS under the new or old regime using FY 2025-26 slabs.',
    'showWorking' => true,
])

<section class="crmleaf-tool crmleaf-tool--tds-calculator" data-crmleaf-tool="tds-calculator">
    <header class="crmleaf-tool__header">
        <h2 class="crmleaf-tool__heading">{{ $heading }}</h2>
        <p class="crmleaf-tool__tagline">{{ $tagline }}</p>
    </header>

    @if ($error)
        <p class="crmleaf-tool__error" role="alert">{{ $error }}</p>
    @endif

    <form class="crmleaf-tool__form"
          method="{{ strtolower($method) === 'get' ? 'get' : 'post' }}"
          action="{{ $action }}"
          data-crmleaf-form>
        @if (strtolower($method) !== 'get')
            @csrf
        @endif

        <label class="crmleaf-field">
            <span>Monthly gross salary</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="monthly_gross" value="{{ old('monthly_gross', $input['monthly_gross'] ?? ($defaults['monthly_gross'] ?? '')) }}" required>
        </label>

        <label class="crmleaf-field">
            <span>Tax regime</span>
            <select name="regime">
                <option value="new" @selected(old('regime', $input['regime'] ?? ($defaults['regime'] ?? '')) === 'new')>New regime (115BAC)</option>
                <option value="old" @selected(old('regime', $input['regime'] ?? ($defaults['regime'] ?? '')) === 'old')>Old regime</option>
            </select>
        </label>

        <label class="crmleaf-field">
            <span>Age</span>
            <input type="number" step="1" inputmode="numeric" name="age" value="{{ old('age', $input['age'] ?? ($defaults['age'] ?? '')) }}">
            <small>Only the old regime varies the exemption limit by age.</small>
        </label>

        <label class="crmleaf-field">
            <span>Deductions claimed</span>
            <textarea name="deductions" rows="4" spellcheck="false">{{ old('deductions', is_array($input['deductions'] ?? null) ? json_encode($input['deductions']) : ($defaults['deductions'] ?? '')) }}</textarea>
            <small>Keyed by section: 80c, 80ccd_1b, 80d_self_below_60, 80tta, 80ttb, 24b_self_occupied, 80ccd_2. Amounts in rupees.</small>
        </label>

        <label class="crmleaf-field">
            <span>Months remaining in the year</span>
            <input type="number" step="1" inputmode="numeric" name="months_remaining" value="{{ old('months_remaining', $input['months_remaining'] ?? ($defaults['months_remaining'] ?? '')) }}">
        </label>

        <label class="crmleaf-field">
            <span>TDS already deducted this year</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="tax_already_deducted" value="{{ old('tax_already_deducted', $input['tax_already_deducted'] ?? ($defaults['tax_already_deducted'] ?? '')) }}">
        </label>

        <label class="crmleaf-field">
            <span>Slabs as on</span>
            <input type="date" name="as_of" value="{{ old('as_of', $input['as_of'] ?? ($defaults['as_of'] ?? '')) }}">
            <small>Set this to recompute a prior year on that year&#039;s slabs.</small>
        </label>

        <input type="hidden" name="tool" value="tds-calculator">

        <div class="crmleaf-tool__actions">
            <button type="submit" class="crmleaf-tool__submit">Calculate</button>
        </div>
    </form>

    {{-- The client-side path writes its answer here; the server-side path fills it below. --}}
    <div class="crmleaf-tool__output" data-crmleaf-output hidden></div>

    @if ($result)
        <div class="crmleaf-tool__result">
            <p class="crmleaf-tool__explain"><code>{{ $result->explain() }}</code></p>

            <table class="crmleaf-tool__figures">
                <tbody>
                @foreach ($result->toArray() as $key => $value)
                    @continue(is_array($value) || str_ends_with((string) $key, '_formatted'))
                    <tr>
                        <th scope="row">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</th>
                        <td>{{ $result->toArray()[$key.'_formatted'] ?? (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if ($showWorking && count($result->steps()))
                <details class="crmleaf-tool__working" open>
                    <summary>How this was worked out</summary>
                    <ol>
                        @foreach ($result->steps() as $step)
                            <li>
                                <span class="crmleaf-step__label">{{ $step->label }}</span>
                                @if ($step->amount)
                                    <span class="crmleaf-step__amount">{{ $step->amount->format() }}</span>
                                @endif
                                @if ($step->formula)
                                    <code class="crmleaf-step__formula">{{ $step->formula }}</code>
                                @endif
                                @if ($step->citation)
                                    <small class="crmleaf-step__citation">{{ $step->citation }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </details>
            @endif

            @if (count($result->citations()))
                <ul class="crmleaf-tool__citations">
                    @foreach ($result->citations() as $citation)
                        <li>{{ $citation }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</section>
