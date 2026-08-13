/*
 * @crmleaf/tds-calculator - a re-export, not a reimplementation.
 *
 * The arithmetic lives once, in @crmleaf/payroll-js, so a slab change cannot
 * land in one package and miss another. This package exists so a project that
 * only wants TDS Calculator can install only TDS Calculator and still get the
 * identical function it would have got from the suite.
 */

export { tds, tds as calculate, Money } from '@crmleaf/payroll-js';

export { tds as default } from '@crmleaf/payroll-js';
