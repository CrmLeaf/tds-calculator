# TDS Calculator

Monthly TDS under the new or old regime using FY 2025-26 slabs.

Works out the tax to deduct from this month's salary by annualising the pay, taxing it properly, subtracting what has already been deducted, and spreading the balance over the months that remain.

One of the [CRMLeaf payroll tools](https://github.com/crmleaf). The arithmetic
and the dated statutory rate tables live in
[`crmleaf/payroll-core`](https://github.com/crmleaf/payroll-core); this package is
the thin skin that makes one calculator installable, mountable and embeddable on
its own.

> [!NOTE]
> A wrong figure or an out-of-date rate is almost always a
> [`payroll-core`](https://github.com/crmleaf/payroll-core/issues) matter, since
> that is where the tables live. Anything about this tool's routes, views or
> browser asset belongs here.

## Install

**Composer** - Laravel auto-discovers the service provider, so this is the whole
setup:

```bash
composer require crmleaf/tds-calculator
```

> [!NOTE]
> Not on Packagist yet. Until it is, point Composer at the two repositories in
> **your own project's** `composer.json` and the same `require` works, because
> Composer reads the tags:
>
> ```json
> "repositories": [
>     { "type": "vcs", "url": "https://github.com/crmleaf/tds-calculator.git" },
>     { "type": "vcs", "url": "https://github.com/crmleaf/payroll-core.git" }
> ]
> ```
>
> Both entries are needed, and they have to be in the root project: Composer
> ignores a `repositories` block inside an installed dependency, so listing only
> this package will not resolve `crmleaf/payroll-core`.

**npm** - the same calculation, re-exported from `@crmleaf/payroll-js` so you can
install this one tool and nothing else:

```bash
npm install @crmleaf/tds-calculator
```

> [!NOTE]
> Not on npm yet either. The script-tag route below needs no registry and works
> today. Installing this package straight from git will not resolve
> `@crmleaf/payroll-js`, for the same reason as above.

**A plain script tag** - no build step, no bundler, no server. Build the browser
bundle once and serve the file yourself:

```html
<script src="/js/payroll.min.js"></script>
<script>
const result = CrmleafPayroll.tds({ monthlyGross: 100000, regime: "new" });
console.log(result.explain);
</script>
```

`payroll.min.js` is the single-file browser build. Get it by running
`npm run build` in [`@crmleaf/payroll-js`][js] and copying `dist/payroll.min.js`
into whatever your site serves as static assets.

> A hosted CDN build is coming soon, which will reduce this to a single URL.
> Serving the file yourself works today and keeps working afterwards - it is the
> only option that needs no third-party request, so plenty of projects will want
> to stay on it.

### See it working first

`demo/index.html` in this repository is a working copy of TDS Calculator in one file:
the form, the calculation and the working, with no build step and no server. Drop
`payroll.min.js` beside it and open it from disk.

```bash
cp /path/to/payroll-js/dist/payroll.min.js demo/
open demo/index.html
```

Nothing on that page reaches the network, which is the point: it is a calculator
people paste salary figures into.

## Use it

**Plain PHP**, no framework and no container:

```php
use Crmleaf\Payroll\Calculators\TdsCalculator;
use Crmleaf\Payroll\Money;

$result = (new TdsCalculator())->calculate(
    monthlyGross: Money::fromRupees(100_000),
    regime: 'new',
);

echo $result->explain();      // the formula with the real operands in it
echo $result->workings();     // every step, one per line, with its citation
print_r($result->toArray());  // snake_case, ready for JSON
```

**Laravel** - resolve it from the container, or type-hint it anywhere:

```php
use Crmleaf\Payroll\Calculators\TdsCalculator;

public function show(TdsCalculator $calculator)
{
    return $calculator->calculate(
        monthlyGross: Money::fromRupees(100_000),
        regime: 'new',
    )->toArray();
}
```

**Blade** - one component, no controller:

```blade
<x-crmleaf::tds-calculator />
```

**HTTP** - off by default. Publish the config and turn the route on:

```bash
php artisan vendor:publish --tag=tds-calculator-config
```

```php
// config/tds-calculator.php
'route' => ['enabled' => true, 'prefix' => 'tools'],
```

```bash
curl -X POST https://example.test/tools/tds-calculator \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"monthly_gross":100000,"regime":"new"}'
```

The JSON response carries the figures, the working and the statutory citations:

```json
{
  "tool": "tds-calculator",
  "data": { "…": "every figure, snake_case, with a *_formatted twin" },
  "explain": "the formula with the real operands substituted",
  "working": [{ "label": "…", "amount": 0, "formula": "…", "citation": "…" }],
  "citations": ["…"]
}
```

**JavaScript**:

```js
import { tds } from '@crmleaf/tds-calculator';

const result = tds({ monthlyGross: 100000, regime: "new" });
```

## No server needed

The maths here is arithmetic over versioned rate tables, so it runs anywhere.
The published asset binds the markup and computes in the browser:

```bash
php artisan vendor:publish --tag=tds-calculator-assets
```

```html
<section data-crmleaf-tool="tds-calculator">
  <form data-crmleaf-form> … </form>
  <div data-crmleaf-output hidden></div>
</section>

<script src="/js/payroll.min.js"></script>
<script src="/vendor/tds-calculator/tds-calculator.js"></script>
```

If the browser build is absent the script does nothing and the form posts to the
server instead, so the page works either way.

## Inputs

| Field | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `monthly_gross` | money (₹) | Yes | `100000` |  |
| `regime` | one of `new`, `old` | No | `"new"` |  |
| `age` | integer | No | `30` | Only the old regime varies the exemption limit by age. |
| `deductions` | array or JSON | No | - | Keyed by section: 80c, 80ccd_1b, 80d_self_below_60, 80tta, 80ttb, 24b_self_occupied, 80ccd_2. Amounts in rupees. |
| `months_remaining` | integer | No | `12` |  |
| `tax_already_deducted` | money (₹) | No | `0` |  |
| `as_of` | date (YYYY-MM-DD) | No | - | Set this to recompute a prior year on that year's slabs. |

Optional fields you leave out are omitted from the call entirely, so the
calculator's own documented defaults apply.

Every figure here rests on a statutory rate, so the call takes `as_of`. Set it
and the calculation runs on the rates in force on that date, which is what makes
a prior year recomputable rather than merely rememberable.

## Statutory basis

Income-tax Act 1961, section 192 read with section 115BAC, on the FY 2025-26 slabs given by the Finance Act 2025, including the section 87A rebate with its marginal relief and section 288B rounding.

Rates are data, not code: they live in dated tables with a cited source in
`crmleaf/payroll-core`, so a rate change is a new dated entry rather than an edit
to a constant.

> [!IMPORTANT]
> This package implements our reading of the applicable statutes and is provided
> without warranty. It is a calculation library, not tax advice. Verify against
> your own compliance obligations before relying on the output for statutory
> filing.

## Publishing

| Tag | Publishes |
|-----|-----------|
| `tds-calculator-config` | `config/tds-calculator.php` |
| `tds-calculator-views` | `resources/views/vendor/tds-calculator` |
| `tds-calculator-assets` | `public/vendor/tds-calculator` |

## Licence

[MIT](LICENSE) © CRMLeaf. Use it commercially, embed it, fork it.

[js]: https://github.com/crmleaf/payroll-js
