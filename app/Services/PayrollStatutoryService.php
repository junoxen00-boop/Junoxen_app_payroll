<?php

namespace App\Services;

use Carbon\Carbon;

class PayrollStatutoryService
{
    /**
     * Telangana Professional Tax in paise.
     */
    public function professionalTaxCents(
        int $monthlySalaryCents
    ): int {
        $salaryCents = max(
            0,
            $monthlySalaryCents
        );

        foreach (
            config(
                'payroll.professional_tax.slabs',
                []
            )
            as $slab
        ) {
            $upTo = $slab['up_to'];

            if (
                $upTo === null
                || $salaryCents <=
                    ((int) $upTo * 100)
            ) {
                return (
                    (int) $slab['tax']
                ) * 100;
            }
        }

        return 0;
    }

    /**
     * Employee PF contribution.
     *
     * Basic Salary entered by the employer is used
     * as the PF wage basis.
     */
    public function providentFundCents(
        int $basicSalaryCents,
        Carbon $periodEnd
    ): int {
        $basicSalaryCents = max(
            0,
            $basicSalaryCents
        );

        $ceilingCents =
            $this->pfWageCeilingRupees(
                $periodEnd
            ) * 100;

        $contributoryWageCents = min(
            $basicSalaryCents,
            $ceilingCents
        );

        $rateBasisPoints =
            $this->percentToBasisPoints(
                (string) config(
                    'payroll.provident_fund.employee_rate_percent',
                    '12'
                )
            );

        $contributionCents =
            $this->divideAndRound(
                $contributoryWageCents
                    * $rateBasisPoints,
                10000
            );

        if (
            (bool) config(
                'payroll.provident_fund.round_to_nearest_rupee',
                true
            )
        ) {
            return $this
                ->roundCentsToNearestRupee(
                    $contributionCents
                );
        }

        return $contributionCents;
    }

    public function pfWageCeilingRupees(
        Carbon $periodEnd
    ): int {
        $selected = 0;

        foreach (
            config(
                'payroll.provident_fund.wage_ceiling_history',
                []
            )
            as $row
        ) {
            $effective = Carbon::parse(
                $row['effective_from']
            )->startOfDay();

            if (
                $effective->lte($periodEnd)
            ) {
                $selected =
                    (int) $row['amount'];
            }
        }

        return $selected > 0
            ? $selected
            : 15000;
    }

    private function percentToBasisPoints(
        string $percent
    ): int {
        $normalized = preg_replace(
            '/[^0-9.]/',
            '',
            $percent
        ) ?: '0';

        [$whole, $fraction] =
            array_pad(
                explode(
                    '.',
                    $normalized,
                    2
                ),
                2,
                ''
            );

        $fraction = substr(
            str_pad(
                $fraction,
                2,
                '0'
            ),
            0,
            2
        );

        return (
            (int) $whole * 100
        ) + (int) $fraction;
    }

    private function divideAndRound(
        int $numerator,
        int $denominator
    ): int {
        if ($denominator <= 0) {
            return 0;
        }

        return intdiv(
            $numerator
                + intdiv(
                    $denominator,
                    2
                ),
            $denominator
        );
    }

    private function roundCentsToNearestRupee(
        int $cents
    ): int {
        return intdiv(
            max(0, $cents) + 50,
            100
        ) * 100;
    }
}