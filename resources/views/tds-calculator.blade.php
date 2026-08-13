<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TDS Calculator' }}</title>
    <meta name="description" content="Monthly TDS under the new or old regime using FY 2025-26 slabs.">
    <style>
        :root { color-scheme: light dark; --crmleaf-ink: #16181d; --crmleaf-muted: #5d636e; --crmleaf-line: #d9dde4; --crmleaf-accent: #1f6feb; --crmleaf-bg: #ffffff; }
        @media (prefers-color-scheme: dark) {
            :root { --crmleaf-ink: #e8eaed; --crmleaf-muted: #9aa1ad; --crmleaf-line: #2c313a; --crmleaf-accent: #6ea8ff; --crmleaf-bg: #14161a; }
        }
        body { margin: 0; padding: 2rem 1rem; background: var(--crmleaf-bg); color: var(--crmleaf-ink);
               font: 16px/1.55 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .crmleaf-tool { max-width: 46rem; margin: 0 auto; }
        .crmleaf-tool__heading { font-size: 1.5rem; margin: 0 0 .25rem; }
        .crmleaf-tool__tagline { color: var(--crmleaf-muted); margin: 0 0 1.5rem; }
        .crmleaf-tool__error { border-left: 3px solid #c0392b; padding: .5rem .75rem; background: rgba(192,57,43,.08); }
        .crmleaf-field { display: block; margin: 0 0 1rem; }
        .crmleaf-field > span { display: block; font-weight: 600; margin-bottom: .25rem; }
        .crmleaf-field input:not([type=checkbox]), .crmleaf-field select, .crmleaf-field textarea {
            width: 100%; padding: .5rem .6rem; border: 1px solid var(--crmleaf-line); border-radius: .35rem;
            background: transparent; color: inherit; font: inherit; }
        .crmleaf-field small { display: block; color: var(--crmleaf-muted); margin-top: .25rem; }
        .crmleaf-field--bool { display: flex; align-items: flex-start; gap: .5rem; }
        .crmleaf-tool__submit { padding: .6rem 1.2rem; border: 0; border-radius: .35rem;
            background: var(--crmleaf-accent); color: #fff; font: inherit; cursor: pointer; }
        .crmleaf-tool__figures { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .crmleaf-tool__figures th, .crmleaf-tool__figures td { text-align: left; padding: .4rem 0; border-bottom: 1px solid var(--crmleaf-line); }
        .crmleaf-tool__figures td { text-align: right; font-variant-numeric: tabular-nums; }
        .crmleaf-tool__working ol { padding-left: 1.2rem; }
        .crmleaf-tool__working li { margin-bottom: .6rem; }
        .crmleaf-step__amount { float: right; font-variant-numeric: tabular-nums; }
        .crmleaf-step__formula, .crmleaf-step__citation { display: block; color: var(--crmleaf-muted); font-size: .85rem; }
        .crmleaf-tool__citations { color: var(--crmleaf-muted); font-size: .85rem; padding-left: 1.2rem; }
        .crmleaf-tool__colophon { max-width: 46rem; margin: 2.5rem auto 0; padding-top: 1rem;
            border-top: 1px solid var(--crmleaf-line); color: var(--crmleaf-muted); font-size: .85rem; }
    </style>
</head>
<body>

<x-crmleaf::tds-calculator
    :action="$action ?? null"
    :defaults="$defaults ?? []"
    :input="$input ?? []"
    :result="$result ?? null"
    :error="$error ?? null"
    :heading="$title ?? 'TDS Calculator'"
/>

<footer class="crmleaf-tool__colophon">
    <p>
        Income-tax Act 1961, section 192 read with section 115BAC, on the FY 2025-26 slabs given by the Finance Act 2025, including the section 87A rebate with its marginal relief and section 288B rounding.
    </p>
    <p>
        Computed by <a href="https://github.com/crmleaf/tds-calculator">crmleaf/tds-calculator</a>,
        MIT licensed. A calculation library, not tax advice - verify against your own
        compliance obligations before filing.
    </p>
</footer>

{{-- No server required. --}}
{{--
    Everything above renders without PHP too. Drop this markup on a static page,
    load the browser build, and the same arithmetic runs client-side:

    <script src="/js/payroll.min.js"></script>
    <script src="/vendor/tds-calculator/tds-calculator.js"></script>

    Build the browser bundle with `npm run build` in @crmleaf/payroll-js and
    serve dist/payroll.min.js yourself. A CDN URL will do the same job once the
    package is published to npm.

    The bundled script binds any element carrying data-crmleaf-tool="tds-calculator",
    computes on submit, and only falls back to posting the form if the browser
    build is not present.
--}}
@if (config('tds-calculator.assets.script', true))
    @if (config('tds-calculator.assets.cdn'))
        <script src="{{ config('tds-calculator.assets.cdn') }}" defer></script>
    @endif
    <script src="{{ asset('vendor/tds-calculator/tds-calculator.js') }}" defer></script>
@endif

</body>
</html>
