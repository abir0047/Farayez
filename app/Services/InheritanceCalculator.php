<?php

namespace App\Services;

class InheritanceCalculator
{
    protected $data;
    protected $totalEstate;
    protected $results = [];
    protected $remainingShareNumerator  = 1;
    protected $remainingShareDenominator   = 1;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->totalEstate = $this->calculateTotalEstate($data['assets']);
    }

    // Modify calculate() method
    public function calculate()
    {
        $this->calculateFixedShares();
        $this->allocateResidue();

        // Asset labels mapping
        $assetLabels = [
            'land' => ['name' => 'জমির পরিমাণ', 'unit' => 'শতাংশ/কাঠা'],
            'flat' => ['name' => 'ফ্ল্যাট', 'unit' => 'স্কয়ার ফিট'],
            'cash' => ['name' => 'নগদ টাকার পরিমাণ', 'unit' => 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত'],
            'investment' => ['name' => 'বিনিয়োগের পরিমাণ', 'unit' => 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত'],
            'owedCash' => ['name' => 'পাওনা টাকার পরিমাণ', 'unit' => 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত'],
            'UnpaidDebt' => ['name' => 'অপরিশোধিত ঋণ', 'unit' => 'টাকায়']
        ];

        $assetDistribution = [];
        foreach ($this->data['assets'] as $assetKey => $asset) {
            if (!array_key_exists($assetKey, $assetLabels)) continue;

            $assetDistribution[$assetKey] = [
                'name' => $assetLabels[$assetKey]['name'],
                'unit' => $assetLabels[$assetKey]['unit'],
                'value' => $asset['value'],
                'shares' => []
            ];

            foreach ($this->results as $share) {
                $share_fraction = $share['numerator'] / $share['denominator'];
                $assetShare = $asset['value'] * $share_fraction;

                $assetDistribution[$assetKey]['shares'][] = [
                    'relation' => $share['relation'],
                    'name' => $share['name'],
                    'amount' => $assetShare,
                    'fraction' => $share_fraction
                ];
            }
        }

        $denominators = array_column($this->results, 'denominator');
        $commonDenominator = $this->computeLCM($denominators);

        // Convert each share to the common denominator
        foreach ($this->results as &$share) {
            $share['common_numerator'] = $share['numerator'] * ($commonDenominator / $share['denominator']);
            $share['common_denominator'] = $commonDenominator;
        }

        return [
            'total_estate' => $this->totalEstate,
            'common_denominator' => $commonDenominator,
            'shares' => $this->results,
            'assets' => $assetDistribution
        ];
    }

    private function calculateFixedShares()
    {
        // 1. Spouse share (correctly handled elsewhere)
        $this->calculateSpouseShare();

        // 2. Father's share (1/6 if descendants exist)
        if ($this->isAlive('aliveParentStatus', 'father')) {
            // Father gets 1/6 if deceased has ANY descendants (sons or daughters)
            if ($this->hasDescendantSons() || $this->hasDescendantDaughters()) {
                $this->addShare(
                    $this->data['heirs']['aliveParentStatus']['father']['label'],
                    1,  // numerator
                    6,  // denominator
                    $this->data['heirs']['aliveParentStatus']['father']['name']
                );

                // Update remaining share using fraction arithmetic
                $this->remainingShareNumerator = $this->remainingShareNumerator * 6 - 1 * $this->remainingShareDenominator;
                $this->remainingShareDenominator *= 6;

                // Simplify fraction
                $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                $this->remainingShareNumerator /= $gcd;
                $this->remainingShareDenominator /= $gcd;
            }
        } elseif ($this->isAlive('aliveGrandParentStatus', 'paternalGrandfather')) {
            // Paternal grandfather inherits only if father is dead
            $this->addShare(
                $this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['label'],
                1,  // numerator
                6,  // denominator
                $this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['name']
            );

            // Update remaining share using fraction arithmetic
            $this->remainingShareNumerator = $this->remainingShareNumerator * 6 - 1 * $this->remainingShareDenominator;
            $this->remainingShareDenominator *= 6;

            // Simplify fraction
            $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
            $this->remainingShareNumerator /= $gcd;
            $this->remainingShareDenominator /= $gcd;
        }

        // 3. Mother's share (FIXED: Check for ANY full siblings, not "more than one")
        if ($this->isAlive('aliveParentStatus', 'mother')) {
            // Mother gets 1/6 if deceased has:
            // - Any descendants (sons/daughters) OR 
            // - ANY full siblings (even one)
            if ($this->hasDescendantSons() || $this->hasDescendantDaughters() || $this->hasAnySiblings()) {
                // Mother gets 1/6
                $this->addShare(
                    $this->data['heirs']['aliveParentStatus']['mother']['label'],
                    1,  // numerator
                    6,  // denominator
                    $this->data['heirs']['aliveParentStatus']['mother']['name']
                );

                // Update remaining share using fraction arithmetic
                $this->remainingShareNumerator = $this->remainingShareNumerator * 6 - 1 * $this->remainingShareDenominator;
                $this->remainingShareDenominator *= 6;

                // Simplify fraction
                $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                $this->remainingShareNumerator /= $gcd;
                $this->remainingShareDenominator /= $gcd;
            } else {
                // Otherwise, mother gets 1/3
                $this->addShare(
                    $this->data['heirs']['aliveParentStatus']['mother']['label'],
                    1,  // numerator
                    3,  // denominator
                    $this->data['heirs']['aliveParentStatus']['mother']['name']
                );

                // Update remaining share using fraction arithmetic
                $this->remainingShareNumerator = $this->remainingShareNumerator * 3 - 1 * $this->remainingShareDenominator;
                $this->remainingShareDenominator *= 3;

                // Simplify fraction
                $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                $this->remainingShareNumerator /= $gcd;
                $this->remainingShareDenominator /= $gcd;
            }
        }

        // 4. Daughter's share (correct)
        if (!$this->hasDescendantSons()) {
            $aliveDaughters = $this->data['heirs']['children']['aliveDaughters']['count'] ?? 0;
            $deceasedDaughters = $this->data['heirs']['children']['deceasedDaughters']['names'] ?? [];

            // Count deceased daughters with living children
            $deceasedWithChildren = array_filter($deceasedDaughters, function ($d) {
                return ($d['sonsCount'] ?? 0) > 0 || ($d['daughtersCount'] ?? 0) > 0;
            });

            $effectiveDaughters = $aliveDaughters + count($deceasedWithChildren);

            if ($effectiveDaughters === 1) {
                $totalDaughterNumerator = 1;
                $totalDaughterDenominator = 2;
            } elseif ($effectiveDaughters > 1) {
                $totalDaughterNumerator = 2;
                $totalDaughterDenominator = 3;
            } else {
                $totalDaughterNumerator = 0;
                $totalDaughterDenominator = 1;
            }

            if ($totalDaughterNumerator > 0) {
                // Subtract the total daughter share from remainingShare (tracked as a fraction)
                $this->remainingShareNumerator = $this->remainingShareNumerator * $totalDaughterDenominator - $totalDaughterNumerator * $this->remainingShareDenominator;
                $this->remainingShareDenominator *= $totalDaughterDenominator;

                // Simplify the remaining share fraction
                $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                $this->remainingShareNumerator /= $gcd;
                $this->remainingShareDenominator /= $gcd;

                // Calculate per-daughter share (fraction)
                $perDaughterNumerator = $totalDaughterNumerator;
                $perDaughterDenominator = $totalDaughterDenominator * $effectiveDaughters;

                // Simplify the per-daughter fraction
                $gcd = $this->computeGCD($perDaughterNumerator, $perDaughterDenominator);
                $perDaughterNumerator /= $gcd;
                $perDaughterDenominator /= $gcd;

                // Distribute to alive daughters
                foreach ($this->data['heirs']['children']['aliveDaughters']['names'] as $index => $daughter) {
                    $label = $this->addOrdinalToLabel($this->data['heirs']['children']['aliveDaughters']['label'], $index);
                    $this->addShare($label, $perDaughterNumerator, $perDaughterDenominator, $daughter['name']);
                }

                foreach ($deceasedWithChildren as $deceasedIndex => $deceased) {
                    // Total parts (sons get 2x, daughters get 1x)
                    $totalParts = ($deceased['sonsCount'] * 2) + $deceased['daughtersCount'];

                    if ($totalParts === 0) {
                        continue; // Skip if no children
                    }

                    // Calculate perEffectiveDaughter as a fraction (e.g., 1/3 for 2 daughters)
                    $perEffectiveDaughterNumerator = $totalDaughterNumerator;
                    $perEffectiveDaughterDenominator = $totalDaughterDenominator * $effectiveDaughters;

                    // Simplify fraction
                    $gcd = $this->computeGCD($perEffectiveDaughterNumerator, $perEffectiveDaughterDenominator);
                    $perEffectiveDaughterNumerator /= $gcd;
                    $perEffectiveDaughterDenominator /= $gcd;

                    // Calculate sonShare (2 parts) and daughterShare (1 part)
                    $sonShareNumerator = $perEffectiveDaughterNumerator * 2;
                    $sonShareDenominator = $perEffectiveDaughterDenominator * $totalParts;

                    $daughterShareNumerator = $perEffectiveDaughterNumerator;
                    $daughterShareDenominator = $perEffectiveDaughterDenominator * $totalParts;

                    // Simplify fractions
                    $gcdSon = $this->computeGCD($sonShareNumerator, $sonShareDenominator);
                    $sonShareNumerator /= $gcdSon;
                    $sonShareDenominator /= $gcdSon;

                    $gcdDaughter = $this->computeGCD($daughterShareNumerator, $daughterShareDenominator);
                    $daughterShareNumerator /= $gcdDaughter;
                    $daughterShareDenominator /= $gcdDaughter;

                    // Assign shares to sons of the deceased daughter
                    foreach ($deceased['sonsNames'] as $sonIndex => $son) {
                        $label = $this->addOrdinalToLabel("মৃত মেয়ের ছেলে", $sonIndex);
                        $this->addShare($label, $sonShareNumerator, $sonShareDenominator, $son['name'] ?? null);
                    }

                    // Assign shares to daughters of the deceased daughter
                    foreach ($deceased['daughtersNames'] as $daughterIndex => $daughter) {
                        $label = $this->addOrdinalToLabel("মৃত মেয়ের মেয়ে", $daughterIndex);
                        $this->addShare($label, $daughterShareNumerator, $daughterShareDenominator, $daughter['name'] ?? null);
                    }
                }
            }
        }

        // 5. Full Sister's share (FIXED: Exclude if full brothers exist)
        if (
            !$this->hasDescendantSons() &&
            !$this->hasDescendantDaughters() &&
            !$this->isAlive('aliveParentStatus', 'father')
        ) {
            $fullBrothersCount = $this->data['heirs']['siblings']['brothers']['count'] ?? 0;

            if ($fullBrothersCount === 0) {
                $sisters = $this->data['heirs']['siblings']['sisters'];
                $fullSisterCount = $sisters['count'] ?? 0;
                $sisterNames = $sisters['names'] ?? [];

                if ($fullSisterCount === 1) {
                    // Single sister gets 1/2
                    $sister = $sisterNames[0] ?? [];
                    $label = $this->addOrdinalToLabel(
                        $sisters['label'] ?? 'সহোদর বোন',
                        0
                    );
                    $this->addShare(
                        $label,
                        1,  // numerator
                        2,  // denominator
                        $sister['name'] ?? null
                    );

                    // Update remaining share using fractions
                    $this->remainingShareNumerator = $this->remainingShareNumerator * 2 - 1 * $this->remainingShareDenominator;
                    $this->remainingShareDenominator *= 2;

                    // Simplify fraction
                    $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                    $this->remainingShareNumerator /= $gcd;
                    $this->remainingShareDenominator /= $gcd;
                } elseif ($fullSisterCount > 1) {
                    // Multiple sisters divide 2/3 equally
                    $totalShareNumerator = 2;
                    $totalShareDenominator = 3;

                    // Calculate per sister share
                    $perSisterNumerator = $totalShareNumerator;
                    $perSisterDenominator = $totalShareDenominator * $fullSisterCount;

                    // Simplify fraction
                    $gcd = $this->computeGCD($perSisterNumerator, $perSisterDenominator);
                    $perSisterNumerator /= $gcd;
                    $perSisterDenominator /= $gcd;

                    foreach ($sisterNames as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $sisters['label'] ?? 'সহোদর বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $perSisterNumerator,
                            $perSisterDenominator,
                            $sister['name'] ?? null
                        );
                    }

                    // Update remaining share
                    $this->remainingShareNumerator = $this->remainingShareNumerator * 3 - 2 * $this->remainingShareDenominator;
                    $this->remainingShareDenominator *= 3;

                    // Simplify fraction
                    $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                    $this->remainingShareNumerator /= $gcd;
                    $this->remainingShareDenominator /= $gcd;
                }
            }
        }


        // 6. Paternal Half-Sister's share (updated logic)
        if (
            !$this->hasDescendantSons()
            && !$this->hasDescendantDaughters()
            && !$this->isAlive('aliveParentStatus', 'father')
        ) {
            $fullBrothersCount = $this->data['heirs']['siblings']['brothers']['count'] ?? 0;
            $fullSistersCount = $this->data['heirs']['siblings']['sisters']['count'] ?? 0;
            $paternalHalfSisters = $this->data['heirs']['alivePaternalHalfSisters'] ?? [];
            $paternalHalfSisterCount = $paternalHalfSisters['count'] ?? 0;
            $paternalHalfSisterNames = $paternalHalfSisters['names'] ?? [];

            // Case 1: No full brothers, but full sisters exist
            if ($fullBrothersCount === 0 && $fullSistersCount >= 1) {
                if ($paternalHalfSisterCount >= 1) {
                    $shareNumerator = 1;
                    $shareDenominator = 6;
                    $sharePerSisterNumerator = $shareNumerator;
                    $sharePerSisterDenominator = $shareDenominator * $paternalHalfSisterCount;

                    // Simplify the per-sister fraction
                    $gcd = $this->computeGCD($sharePerSisterNumerator, $sharePerSisterDenominator);
                    $sharePerSisterNumerator /= $gcd;
                    $sharePerSisterDenominator /= $gcd;

                    foreach ($paternalHalfSisterNames as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $paternalHalfSisters['label'] ?? 'বৈমাতৃয় বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $sharePerSisterNumerator,
                            $sharePerSisterDenominator,
                            $sister['name'] ?? null
                        );
                    }

                    // Update remaining share using fraction arithmetic
                    $this->remainingShareNumerator = $this->remainingShareNumerator * $shareDenominator - $shareNumerator * $this->remainingShareDenominator;
                    $this->remainingShareDenominator *= $shareDenominator;
                    $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                    $this->remainingShareNumerator /= $gcd;
                    $this->remainingShareDenominator /= $gcd;
                }
            }
            // Case 2: No full siblings at all
            elseif ($fullBrothersCount === 0 && $fullSistersCount === 0) {
                if ($paternalHalfSisterCount === 1) {
                    $sister = $paternalHalfSisterNames[0] ?? [];
                    $label = $this->addOrdinalToLabel(
                        $paternalHalfSisters['label'] ?? 'বৈমাতৃয় বোন',
                        0
                    );
                    $this->addShare(
                        $label,
                        1, // numerator
                        2, // denominator
                        $sister['name'] ?? null
                    );

                    // Update remaining share
                    $this->remainingShareNumerator = $this->remainingShareNumerator * 2 - 1 * $this->remainingShareDenominator;
                    $this->remainingShareDenominator *= 2;
                    $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                    $this->remainingShareNumerator /= $gcd;
                    $this->remainingShareDenominator /= $gcd;
                } elseif ($paternalHalfSisterCount > 1) {
                    $shareNumerator = 2;
                    $shareDenominator = 3;
                    $sharePerSisterNumerator = $shareNumerator;
                    $sharePerSisterDenominator = $shareDenominator * $paternalHalfSisterCount;

                    // Simplify the per-sister fraction
                    $gcd = $this->computeGCD($sharePerSisterNumerator, $sharePerSisterDenominator);
                    $sharePerSisterNumerator /= $gcd;
                    $sharePerSisterDenominator /= $gcd;

                    foreach ($paternalHalfSisterNames as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $paternalHalfSisters['label'] ?? 'বৈমাতৃয় বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $sharePerSisterNumerator,
                            $sharePerSisterDenominator,
                            $sister['name'] ?? null
                        );
                    }

                    // Update remaining share
                    $this->remainingShareNumerator = $this->remainingShareNumerator * 3 - 2 * $this->remainingShareDenominator;
                    $this->remainingShareDenominator *= 3;
                    $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                    $this->remainingShareNumerator /= $gcd;
                    $this->remainingShareDenominator /= $gcd;
                }
            }
        }

        // 7. Maternal Half-Sibling's share (FIXED: Check for NO parents/children)
        if (
            !$this->isAlive('aliveParentStatus', 'father')
            && !$this->isAlive('aliveParentStatus', 'mother')
            && !$this->hasDescendantSons()
            && !$this->hasDescendantDaughters()
        ) {
            $maternalBrothers = $this->data['heirs']['otherRelatives']['maternalHalfBrother'] ?? [];
            $maternalSisters = $this->data['heirs']['otherRelatives']['maternalHalfSister'] ?? [];

            $brotherCount = $maternalBrothers['count'] ?? 0;
            $sisterCount = $maternalSisters['count'] ?? 0;
            $totalSiblings = $brotherCount + $sisterCount;

            if ($totalSiblings > 0) {
                // Determine total share as fraction (1/6 for single, 1/3 for multiple)
                $totalShareNumerator = $totalSiblings === 1 ? 1 : 1;
                $totalShareDenominator = $totalSiblings === 1 ? 6 : 3;

                $totalParts = ($brotherCount * 2) + $sisterCount;

                if ($totalParts > 0) {
                    // Calculate part value as fraction
                    $partValueNumerator = $totalShareNumerator;
                    $partValueDenominator = $totalShareDenominator * $totalParts;

                    // Simplify part value fraction
                    $gcd = $this->computeGCD($partValueNumerator, $partValueDenominator);
                    $partValueNumerator /= $gcd;
                    $partValueDenominator /= $gcd;

                    // Process brothers with 2:1 ratio (each brother gets 2 parts)
                    foreach ($maternalBrothers['names'] as $index => $brother) {
                        $brotherNumerator = $partValueNumerator * 2;
                        $brotherDenominator = $partValueDenominator;

                        // Simplify brother's fraction
                        $gcdBrother = $this->computeGCD($brotherNumerator, $brotherDenominator);
                        $brotherNumerator /= $gcdBrother;
                        $brotherDenominator /= $gcdBrother;

                        $label = $this->addOrdinalToLabel(
                            $maternalBrothers['label'] ?? 'বৈপিত্রেয় ভাই',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $brotherNumerator,
                            $brotherDenominator,
                            $brother['name'] ?? null
                        );
                    }

                    // Process sisters (each sister gets 1 part)
                    foreach ($maternalSisters['names'] as $index => $sister) {
                        $sisterNumerator = $partValueNumerator;
                        $sisterDenominator = $partValueDenominator;

                        // Simplify sister's fraction (though it may already be simplified)
                        $gcdSister = $this->computeGCD($sisterNumerator, $sisterDenominator);
                        $sisterNumerator /= $gcdSister;
                        $sisterDenominator /= $gcdSister;

                        $label = $this->addOrdinalToLabel(
                            $maternalSisters['label'] ?? 'বৈপিত্রেয় বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $sisterNumerator,
                            $sisterDenominator,
                            $sister['name'] ?? null
                        );
                    }

                    // Update remaining share using fraction arithmetic
                    $this->remainingShareNumerator = $this->remainingShareNumerator * $totalShareDenominator
                        - $totalShareNumerator * $this->remainingShareDenominator;
                    $this->remainingShareDenominator *= $totalShareDenominator;

                    // Simplify remaining share fraction
                    $gcdRemaining = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                    $this->remainingShareNumerator /= $gcdRemaining;
                    $this->remainingShareDenominator /= $gcdRemaining;
                }
            }
        }
    }


    private function calculateSpouseShare()
    {
        $hasChildren = $this->hasChildren();
        $gender = $this->data['deceasedInfo']['gender'];

        if ($gender === 'male') {
            // Wives' share
            $wifeCount = $this->data['heirs']['spouseWives']['count'] ?? 0;
            if ($wifeCount > 0) {
                // Determine share fraction
                $shareNumerator = 1;
                $shareDenominator = $hasChildren ? 8 : 4;

                // Calculate individual wife's share
                $individualNumerator = $shareNumerator;
                $individualDenominator = $shareDenominator * $wifeCount;

                // Simplify the fraction
                $gcd = $this->computeGCD($individualNumerator, $individualDenominator);
                $individualNumerator /= $gcd;
                $individualDenominator /= $gcd;

                foreach ($this->data['heirs']['spouseWives']['names'] as $index => $wife) {
                    $label = $this->addOrdinalToLabel("মৃত ব্যক্তির জীবিত স্ত্রী", $index);
                    $this->addShare(
                        $label,
                        $individualNumerator,
                        $individualDenominator,
                        $wife['name']
                    );
                }

                // Update remaining share using fraction arithmetic
                $this->remainingShareNumerator = $this->remainingShareNumerator * $shareDenominator
                    - $shareNumerator * $this->remainingShareDenominator;
                $this->remainingShareDenominator *= $shareDenominator;

                // Simplify the remaining share
                $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                $this->remainingShareNumerator /= $gcd;
                $this->remainingShareDenominator /= $gcd;
            }
        } else {
            // Husband's share
            if ($this->data['heirs']['spouseStatus'] === 'alive') {
                $shareNumerator = 1;
                $shareDenominator = $hasChildren ? 4 : 2;

                $this->addShare(
                    'মৃত ব্যক্তির জীবিত স্বামী',
                    $shareNumerator,
                    $shareDenominator,
                    $this->data['heirs']['spouseName']
                );

                // Update remaining share using fraction arithmetic
                $this->remainingShareNumerator = $this->remainingShareNumerator * $shareDenominator
                    - $shareNumerator * $this->remainingShareDenominator;
                $this->remainingShareDenominator *= $shareDenominator;

                // Simplify the remaining share
                $gcd = $this->computeGCD($this->remainingShareNumerator, $this->remainingShareDenominator);
                $this->remainingShareNumerator /= $gcd;
                $this->remainingShareDenominator /= $gcd;
            }
        }
    }


    private function allocateResidue()
    {
        if ($this->remainingShareNumerator > 0) {
            // 1. Descendant sons or their sons
            if ($this->hasDescendantSons()) {
                $this->distributeResidueAmongChildren();
            }
            // 2. Father
            elseif ($this->isAlive('aliveParentStatus', 'father')) {
                $this->addShare(
                    $this->data['heirs']['aliveParentStatus']['father']['label'],
                    $this->remainingShareNumerator,
                    $this->remainingShareDenominator,
                    $this->data['heirs']['aliveParentStatus']['father']['name']
                );
                $this->remainingShareNumerator = 0;
                $this->remainingShareDenominator = 1;
            }
            // 3. Paternal grandfather
            elseif ($this->isAlive('aliveGrandParentStatus', 'paternalGrandfather')) {
                $this->addShare(
                    $this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['label'],
                    $this->remainingShareNumerator,
                    $this->remainingShareDenominator,
                    $this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['name']
                );
                $this->remainingShareNumerator = 0;
                $this->remainingShareDenominator = 1;
            }
            // 4. Full siblings or their sons
            elseif ($this->hasFullSiblingsOrDescendants()) {
                $this->distributeResidueToFullSiblings();
            }
            // 5. Paternal half-siblings or their sons
            elseif ($this->hasPaternalHalfSiblingsOrDescendants()) {
                $this->distributeResidueToPaternalHalfSiblings();
            }
            // 6. Full paternal uncles or their sons
            elseif ($this->hasFullPaternalUnclesOrDescendants()) {
                $this->distributeResidueToFullPaternalUncles();
            }
            // 7. Half paternal uncles or their sons
            elseif ($this->hasHalfPaternalUnclesOrDescendants()) {
                $this->distributeResidueToHalfPaternalUncles();
            } else {
                // Handle cousins or other agnatic heirs if needed
                // Could implement additional distribution logic here
            }
        }
    }

    private function distributeResidueAmongChildren()
    {
        // Calculate effective sons/daughters (alive + deceased with children)
        $aliveSons = $this->data['heirs']['children']['aliveSons']['count'] ?? 0;
        $aliveDaughters = $this->data['heirs']['children']['aliveDaughters']['count'] ?? 0;

        $deceasedSons = array_filter(
            $this->data['heirs']['children']['deceasedSons']['names'] ?? [],
            fn($s) => ($s['sonsCount'] ?? 0) + ($s['daughtersCount'] ?? 0) > 0
        );

        $deceasedDaughters = array_filter(
            $this->data['heirs']['children']['deceasedDaughters']['names'] ?? [],
            fn($d) => ($d['sonsCount'] ?? 0) + ($d['daughtersCount'] ?? 0) > 0
        );

        $effectiveSons = $aliveSons + count($deceasedSons);
        $effectiveDaughters = $aliveDaughters + count($deceasedDaughters);

        if ($effectiveSons + $effectiveDaughters === 0) {
            return;
        }

        // Calculate total parts based on 2:1 ratio (sons get 2 parts, daughters get 1)
        $totalParts = ($effectiveSons * 2) + $effectiveDaughters;

        // Calculate part value as a fraction
        $partValueNumerator = $this->remainingShareNumerator;
        $partValueDenominator = $this->remainingShareDenominator * $totalParts;

        // Simplify the part value fraction
        $gcd = $this->computeGCD($partValueNumerator, $partValueDenominator);
        $partValueNumerator /= $gcd;
        $partValueDenominator /= $gcd;

        // Distribute to alive sons (2 parts each)
        foreach ($this->data['heirs']['children']['aliveSons']['names'] as $index => $son) {
            $sonNumerator = $partValueNumerator * 2;
            $sonDenominator = $partValueDenominator;

            // Simplify son's fraction
            $sonGcd = $this->computeGCD($sonNumerator, $sonDenominator);
            $sonNumerator /= $sonGcd;
            $sonDenominator /= $sonGcd;

            $label = $this->addOrdinalToLabel($this->data['heirs']['children']['aliveSons']['label'], $index);
            $this->addShare($label, $sonNumerator, $sonDenominator, $son['name']);
        }

        // Distribute to alive daughters (1 part each)
        foreach ($this->data['heirs']['children']['aliveDaughters']['names'] as $index => $daughter) {
            $daughterNumerator = $partValueNumerator;
            $daughterDenominator = $partValueDenominator;

            // Simplify daughter's fraction (though it may already be simplified)
            $daughterGcd = $this->computeGCD($daughterNumerator, $daughterDenominator);
            $daughterNumerator /= $daughterGcd;
            $daughterDenominator /= $daughterGcd;

            $label = $this->addOrdinalToLabel($this->data['heirs']['children']['aliveDaughters']['label'], $index);
            $this->addShare($label, $daughterNumerator, $daughterDenominator, $daughter['name']);
        }

        // Distribute to deceased sons' children
        foreach ($deceasedSons as $deceasedSon) {
            $totalChildParts = ($deceasedSon['sonsCount'] * 2) + $deceasedSon['daughtersCount'];
            if ($totalChildParts === 0) continue;

            // Calculate deceased son's share (2 parts)
            $deceasedSonNumerator = $partValueNumerator * 2;
            $deceasedSonDenominator = $partValueDenominator;

            // Sons of deceased son (get 2 parts each)
            foreach ($deceasedSon['sonsNames'] as $sonIndex => $grandson) {
                $grandsonNumerator = $deceasedSonNumerator * 2;
                $grandsonDenominator = $deceasedSonDenominator * $totalChildParts;

                // Simplify grandson's fraction
                $grandsonGcd = $this->computeGCD($grandsonNumerator, $grandsonDenominator);
                $grandsonNumerator /= $grandsonGcd;
                $grandsonDenominator /= $grandsonGcd;

                $label = $this->addOrdinalToLabel("মৃত ছেলের ছেলে", $sonIndex);
                $this->addShare($label, $grandsonNumerator, $grandsonDenominator, $grandson['name'] ?? null);
            }

            // Daughters of deceased son (get 1 part each)
            foreach ($deceasedSon['daughtersNames'] as $daughterIndex => $granddaughter) {
                $granddaughterNumerator = $deceasedSonNumerator;
                $granddaughterDenominator = $deceasedSonDenominator * $totalChildParts;

                // Simplify granddaughter's fraction
                $granddaughterGcd = $this->computeGCD($granddaughterNumerator, $granddaughterDenominator);
                $granddaughterNumerator /= $granddaughterGcd;
                $granddaughterDenominator /= $granddaughterGcd;

                $label = $this->addOrdinalToLabel("মৃত ছেলের মেয়ে", $daughterIndex);
                $this->addShare($label, $granddaughterNumerator, $granddaughterDenominator, $granddaughter['name'] ?? null);
            }
        }

        // Distribute to deceased daughters' children
        foreach ($deceasedDaughters as $deceasedDaughter) {
            $totalChildParts = ($deceasedDaughter['sonsCount'] * 2) + $deceasedDaughter['daughtersCount'];
            if ($totalChildParts === 0) continue;

            // Calculate deceased daughter's share (1 part)
            $deceasedDaughterNumerator = $partValueNumerator;
            $deceasedDaughterDenominator = $partValueDenominator;

            // Sons of deceased daughter (get 2 parts each)
            foreach ($deceasedDaughter['sonsNames'] as $sonIndex => $grandson) {
                $grandsonNumerator = $deceasedDaughterNumerator * 2;
                $grandsonDenominator = $deceasedDaughterDenominator * $totalChildParts;

                // Simplify grandson's fraction
                $grandsonGcd = $this->computeGCD($grandsonNumerator, $grandsonDenominator);
                $grandsonNumerator /= $grandsonGcd;
                $grandsonDenominator /= $grandsonGcd;

                $label = $this->addOrdinalToLabel("মৃত মেয়ের ছেলে", $sonIndex);
                $this->addShare($label, $grandsonNumerator, $grandsonDenominator, $grandson['name'] ?? null);
            }

            // Daughters of deceased daughter (get 1 part each)
            foreach ($deceasedDaughter['daughtersNames'] as $daughterIndex => $granddaughter) {
                $granddaughterNumerator = $deceasedDaughterNumerator;
                $granddaughterDenominator = $deceasedDaughterDenominator * $totalChildParts;

                // Simplify granddaughter's fraction
                $granddaughterGcd = $this->computeGCD($granddaughterNumerator, $granddaughterDenominator);
                $granddaughterNumerator /= $granddaughterGcd;
                $granddaughterDenominator /= $granddaughterGcd;

                $label = $this->addOrdinalToLabel("মৃত মেয়ের মেয়ে", $daughterIndex);
                $this->addShare($label, $granddaughterNumerator, $granddaughterDenominator, $granddaughter['name'] ?? null);
            }
        }

        // Update remaining share to 0 since we've distributed all
        $this->remainingShareNumerator = 0;
        $this->remainingShareDenominator = 1;
    }

    private function hasFullSiblingsOrDescendants()
    {
        $aliveBrothers = $this->data['heirs']['siblings']['brothers']['count'] ?? 0;
        $aliveSisters = $this->data['heirs']['siblings']['sisters']['count'] ?? 0;

        // Check for deceased brothers with sons (if data allows)
        $deceasedBrothersWithSons = array_filter(
            $this->data['heirs']['siblings']['deceasedBrothers'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        return $aliveBrothers + count($deceasedBrothersWithSons) + $aliveSisters > 0;
    }

    private function distributeResidueToFullSiblings()
    {
        $brothersData = $this->data['heirs']['siblings']['brothers'] ?? [];
        $sistersData = $this->data['heirs']['siblings']['sisters'] ?? [];

        // Alive brothers/sisters
        $aliveBrothersCount = $brothersData['count'] ?? 0;
        $aliveSistersCount = $sistersData['count'] ?? 0;

        // Deceased brothers with sons
        $deceasedBrothersWithSons = array_filter(
            $brothersData['deceasedBrothers'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        $effectiveBrothers = $aliveBrothersCount + count($deceasedBrothersWithSons);
        $effectiveSisters = $aliveSistersCount;

        if ($effectiveBrothers + $effectiveSisters === 0) {
            return;
        }

        $totalParts = ($effectiveBrothers * 2) + $effectiveSisters;

        // Calculate part value as fraction
        $partValueNumerator = $this->remainingShareNumerator;
        $partValueDenominator = $this->remainingShareDenominator * $totalParts;

        // Simplify part value fraction
        $gcd = $this->computeGCD($partValueNumerator, $partValueDenominator);
        $partValueNumerator /= $gcd;
        $partValueDenominator /= $gcd;

        // Alive brothers (2 parts each)
        foreach (array_slice($brothersData['names'] ?? [], 0, $aliveBrothersCount) as $index => $brother) {
            $brotherNumerator = $partValueNumerator * 2;
            $brotherDenominator = $partValueDenominator;

            // Simplify brother's fraction
            $brotherGcd = $this->computeGCD($brotherNumerator, $brotherDenominator);
            $brotherNumerator /= $brotherGcd;
            $brotherDenominator /= $brotherGcd;

            $label = $this->addOrdinalToLabel(
                $brothersData['label'] ?? 'সহোদর ভাই',
                $index
            );
            $this->addShare(
                $label,
                $brotherNumerator,
                $brotherDenominator,
                $brother['name'] ?? null
            );
        }

        // Deceased brothers' sons
        foreach ($deceasedBrothersWithSons as $deceasedBrother) {
            $sonsCount = $deceasedBrother['sonsCount'] ?? 0;
            $totalSonsParts = $sonsCount * 2;

            if ($totalSonsParts > 0) {
                // Calculate share per son (deceased brother had 2 parts)
                $sonShareNumerator = $partValueNumerator * 2;
                $sonShareDenominator = $partValueDenominator * $totalSonsParts;

                // Simplify son's fraction
                $sonGcd = $this->computeGCD($sonShareNumerator, $sonShareDenominator);
                $sonShareNumerator /= $sonGcd;
                $sonShareDenominator /= $sonGcd;

                foreach (array_slice($deceasedBrother['sonsNames'] ?? [], 0, $sonsCount) as $sonIndex => $son) {
                    $label = $this->addOrdinalToLabel(
                        'মৃত ভাইয়ের ছেলে',
                        $sonIndex
                    );
                    $this->addShare(
                        $label,
                        $sonShareNumerator,
                        $sonShareDenominator,
                        $son['name'] ?? null
                    );
                }
            }
        }

        // Alive sisters (1 part each)
        foreach (array_slice($sistersData['names'] ?? [], 0, $aliveSistersCount) as $index => $sister) {
            $sisterNumerator = $partValueNumerator;
            $sisterDenominator = $partValueDenominator;

            // Simplify sister's fraction
            $sisterGcd = $this->computeGCD($sisterNumerator, $sisterDenominator);
            $sisterNumerator /= $sisterGcd;
            $sisterDenominator /= $sisterGcd;

            $label = $this->addOrdinalToLabel(
                $sistersData['label'] ?? 'সহোদর বোন',
                $index
            );
            $this->addShare(
                $label,
                $sisterNumerator,
                $sisterDenominator,
                $sister['name'] ?? null
            );
        }

        // Reset remaining share
        $this->remainingShareNumerator = 0;
        $this->remainingShareDenominator = 1;
    }

    private function hasPaternalHalfSiblingsOrDescendants()
    {
        $halfBrothers = $this->data['heirs']['otherRelatives']['paternalHalfBrother']['count'] ?? 0;
        $halfSisters = $this->data['heirs']['otherRelatives']['paternalHalfSister']['count'] ?? 0;

        // Check deceased half-brothers with sons
        $deceasedHalfBrothersWithSons = array_filter(
            $this->data['heirs']['otherRelatives']['paternalHalfBrother']['deceased'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        return $halfBrothers + count($deceasedHalfBrothersWithSons) + $halfSisters > 0;
    }

    private function distributeResidueToPaternalHalfSiblings()
    {
        $halfBrotherData = $this->data['heirs']['otherRelatives']['paternalHalfBrother'] ?? [];
        $halfSisterData = $this->data['heirs']['otherRelatives']['paternalHalfSister'] ?? [];

        $aliveHalfBrothersCount = $halfBrotherData['count'] ?? 0;
        $aliveHalfSistersCount = $halfSisterData['count'] ?? 0;

        // Deceased half-brothers with sons
        $deceasedHalfBrothersWithSons = array_filter(
            $halfBrotherData['deceased'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        $effectiveHalfBrothers = $aliveHalfBrothersCount + count($deceasedHalfBrothersWithSons);
        $effectiveHalfSisters = $aliveHalfSistersCount;

        if ($effectiveHalfBrothers + $effectiveHalfSisters === 0) {
            return;
        }

        $totalParts = ($effectiveHalfBrothers * 2) + $effectiveHalfSisters;

        // Calculate part value as fraction
        $partValueNumerator = $this->remainingShareNumerator;
        $partValueDenominator = $this->remainingShareDenominator * $totalParts;

        // Simplify part value fraction
        $gcd = $this->computeGCD($partValueNumerator, $partValueDenominator);
        $partValueNumerator /= $gcd;
        $partValueDenominator /= $gcd;

        // Alive paternal half-brothers (2 parts each)
        foreach (array_slice($halfBrotherData['names'] ?? [], 0, $aliveHalfBrothersCount) as $index => $brother) {
            $brotherNumerator = $partValueNumerator * 2;
            $brotherDenominator = $partValueDenominator;

            // Simplify brother's fraction
            $brotherGcd = $this->computeGCD($brotherNumerator, $brotherDenominator);
            $brotherNumerator /= $brotherGcd;
            $brotherDenominator /= $brotherGcd;

            $label = $this->addOrdinalToLabel(
                $halfBrotherData['label'] ?? 'বৈমাতৃয় ভাই',
                $index
            );
            $this->addShare(
                $label,
                $brotherNumerator,
                $brotherDenominator,
                $brother['name'] ?? null
            );
        }

        // Deceased half-brothers' sons
        foreach ($deceasedHalfBrothersWithSons as $deceasedBrother) {
            $sonsCount = $deceasedBrother['sonsCount'] ?? 0;
            $totalSonsParts = $sonsCount * 2;

            if ($totalSonsParts > 0) {
                // Calculate share per son (deceased brother had 2 parts)
                $sonShareNumerator = $partValueNumerator * 2;
                $sonShareDenominator = $partValueDenominator * $totalSonsParts;

                // Simplify son's fraction
                $sonGcd = $this->computeGCD($sonShareNumerator, $sonShareDenominator);
                $sonShareNumerator /= $sonGcd;
                $sonShareDenominator /= $sonGcd;

                foreach (array_slice($deceasedBrother['sonsNames'] ?? [], 0, $sonsCount) as $sonIndex => $son) {
                    $label = $this->addOrdinalToLabel(
                        'মৃত বৈমাতৃয় ভাইয়ের ছেলে',
                        $sonIndex
                    );
                    $this->addShare(
                        $label,
                        $sonShareNumerator,
                        $sonShareDenominator,
                        $son['name'] ?? null
                    );
                }
            }
        }

        // Alive paternal half-sisters (1 part each)
        foreach (array_slice($halfSisterData['names'] ?? [], 0, $aliveHalfSistersCount) as $index => $sister) {
            $sisterNumerator = $partValueNumerator;
            $sisterDenominator = $partValueDenominator;

            // Simplify sister's fraction
            $sisterGcd = $this->computeGCD($sisterNumerator, $sisterDenominator);
            $sisterNumerator /= $sisterGcd;
            $sisterDenominator /= $sisterGcd;

            $label = $this->addOrdinalToLabel(
                $halfSisterData['label'] ?? 'বৈমাতৃয় বোন',
                $index
            );
            $this->addShare(
                $label,
                $sisterNumerator,
                $sisterDenominator,
                $sister['name'] ?? null
            );
        }

        // Reset remaining share
        $this->remainingShareNumerator = 0;
        $this->remainingShareDenominator = 1;
    }

    private function hasFullPaternalUnclesOrDescendants()
    {
        $paternalUncle = $this->data['heirs']['otherRelatives']['paternalUncle'] ?? [];

        // Check alive uncles
        if (($paternalUncle['count'] ?? 0) > 0) {
            return true;
        }

        // Check deceased uncles with sons
        if (($paternalUncle['hasSons'] ?? 'no') === 'yes' && ($paternalUncle['sonsCount'] ?? 0) > 0) {
            return true;
        }

        // Check for grandsons if no sons
        if (($paternalUncle['sonsCount'] ?? 0) === 0 &&
            ($paternalUncle['hasGrandsons'] ?? 'no') === 'yes' &&
            ($paternalUncle['grandsonsCount'] ?? 0) > 0
        ) {
            return true;
        }

        return false;
    }

    private function distributeResidueToFullPaternalUncles()
    {
        $paternalUncle = $this->data['heirs']['otherRelatives']['paternalUncle'] ?? [];

        // Case 1: Alive paternal uncles
        $aliveCount = $paternalUncle['count'] ?? 0;
        if ($aliveCount > 0) {
            $shareNumerator = $this->remainingShareNumerator;
            $shareDenominator = $this->remainingShareDenominator * $aliveCount;

            // Simplify the per-uncle fraction
            $gcd = $this->computeGCD($shareNumerator, $shareDenominator);
            $shareNumerator /= $gcd;
            $shareDenominator /= $gcd;

            foreach (array_slice($paternalUncle['names'] ?? [], 0, $aliveCount) as $index => $uncle) {
                $label = $this->addOrdinalToLabel(
                    $paternalUncle['label'] ?? 'চাচা',
                    $index
                );
                $this->addShare(
                    $label,
                    $shareNumerator,
                    $shareDenominator,
                    $uncle['name'] ?? null
                );
            }

            $this->remainingShareNumerator = 0;
            $this->remainingShareDenominator = 1;
            return;
        }

        // Case 2: Deceased uncles with living sons
        $sonsCount = $paternalUncle['sonsCount'] ?? 0;
        if (($paternalUncle['hasSons'] ?? 'no') === 'yes' && $sonsCount > 0) {
            $shareNumerator = $this->remainingShareNumerator;
            $shareDenominator = $this->remainingShareDenominator * $sonsCount;

            // Simplify the per-son fraction
            $gcd = $this->computeGCD($shareNumerator, $shareDenominator);
            $shareNumerator /= $gcd;
            $shareDenominator /= $gcd;

            foreach (array_slice($paternalUncle['sonsNames'] ?? [], 0, $sonsCount) as $index => $son) {
                $label = $this->addOrdinalToLabel(
                    'মৃত চাচার ছেলে',
                    $index
                );
                $this->addShare(
                    $label,
                    $shareNumerator,
                    $shareDenominator,
                    $son['name'] ?? null
                );
            }

            $this->remainingShareNumerator = 0;
            $this->remainingShareDenominator = 1;
            return;
        }

        // Case 3: Deceased uncles' grandsons
        $grandsonsCount = $paternalUncle['grandsonsCount'] ?? 0;
        if (($paternalUncle['hasGrandsons'] ?? 'no') === 'yes' && $grandsonsCount > 0) {
            $shareNumerator = $this->remainingShareNumerator;
            $shareDenominator = $this->remainingShareDenominator * $grandsonsCount;

            // Simplify the per-grandson fraction
            $gcd = $this->computeGCD($shareNumerator, $shareDenominator);
            $shareNumerator /= $gcd;
            $shareDenominator /= $gcd;

            foreach (array_slice($paternalUncle['grandsonsNames'] ?? [], 0, $grandsonsCount) as $index => $grandson) {
                $label = $this->addOrdinalToLabel(
                    'মৃত চাচার নাতি',
                    $index
                );
                $this->addShare(
                    $label,
                    $shareNumerator,
                    $shareDenominator,
                    $grandson['name'] ?? null
                );
            }

            $this->remainingShareNumerator = 0;
            $this->remainingShareDenominator = 1;
            return;
        }
    }

    private function distributeResidueToHalfPaternalUncles()
    {
        $halfUncle = $this->data['heirs']['otherRelatives']['paternalHalfUncle'] ?? [];

        // Case 1: Alive half-uncles
        $aliveCount = $halfUncle['count'] ?? 0;
        if ($aliveCount > 0) {
            $shareNumerator = $this->remainingShareNumerator;
            $shareDenominator = $this->remainingShareDenominator * $aliveCount;

            // Simplify the per-uncle fraction
            $gcd = $this->computeGCD($shareNumerator, $shareDenominator);
            $shareNumerator /= $gcd;
            $shareDenominator /= $gcd;

            foreach (array_slice($halfUncle['names'] ?? [], 0, $aliveCount) as $index => $uncle) {
                $label = $this->addOrdinalToLabel(
                    $halfUncle['label'] ?? 'বৈমাতৃয় চাচা',
                    $index
                );
                $this->addShare(
                    $label,
                    $shareNumerator,
                    $shareDenominator,
                    $uncle['name'] ?? null
                );
            }

            $this->remainingShareNumerator = 0;
            $this->remainingShareDenominator = 1;
            return;
        }

        // Case 2: Deceased half-uncles with living sons
        $sonsCount = $halfUncle['sonsCount'] ?? 0;
        if (($halfUncle['hasSons'] ?? 'no') === 'yes' && $sonsCount > 0) {
            $shareNumerator = $this->remainingShareNumerator;
            $shareDenominator = $this->remainingShareDenominator * $sonsCount;

            // Simplify the per-son fraction
            $gcd = $this->computeGCD($shareNumerator, $shareDenominator);
            $shareNumerator /= $gcd;
            $shareDenominator /= $gcd;

            foreach (array_slice($halfUncle['sonsNames'] ?? [], 0, $sonsCount) as $index => $son) {
                $label = $this->addOrdinalToLabel(
                    'মৃত বৈমাতৃয় চাচার ছেলে',
                    $index
                );
                $this->addShare(
                    $label,
                    $shareNumerator,
                    $shareDenominator,
                    $son['name'] ?? null
                );
            }

            $this->remainingShareNumerator = 0;
            $this->remainingShareDenominator = 1;
            return;
        }

        // Case 3: Deceased half-uncles' grandsons
        $grandsonsCount = $halfUncle['grandsonsCount'] ?? 0;
        if (($halfUncle['hasGrandsons'] ?? 'no') === 'yes' && $grandsonsCount > 0) {
            $shareNumerator = $this->remainingShareNumerator;
            $shareDenominator = $this->remainingShareDenominator * $grandsonsCount;

            // Simplify the per-grandson fraction
            $gcd = $this->computeGCD($shareNumerator, $shareDenominator);
            $shareNumerator /= $gcd;
            $shareDenominator /= $gcd;

            foreach (array_slice($halfUncle['grandsonsNames'] ?? [], 0, $grandsonsCount) as $index => $grandson) {
                $label = $this->addOrdinalToLabel(
                    'মৃত বৈমাতৃয় চাচার নাতি',
                    $index
                );
                $this->addShare(
                    $label,
                    $shareNumerator,
                    $shareDenominator,
                    $grandson['name'] ?? null
                );
            }

            $this->remainingShareNumerator = 0;
            $this->remainingShareDenominator = 1;
            return;
        }
    }
    private function hasHalfPaternalUnclesOrDescendants()
    {
        $halfUncle = $this->data['heirs']['otherRelatives']['paternalHalfUncle'] ?? [];

        // Check alive half-uncles
        if (($halfUncle['count'] ?? 0) > 0) {
            return true;
        }

        // Check deceased half-uncles with sons
        if (($halfUncle['hasSons'] ?? 'no') === 'yes' && ($halfUncle['sonsCount'] ?? 0) > 0) {
            return true;
        }

        // Check for grandsons if no sons
        if (($halfUncle['sonsCount'] ?? 0) === 0 &&
            ($halfUncle['hasGrandsons'] ?? 'no') === 'yes' &&
            ($halfUncle['grandsonsCount'] ?? 0) > 0
        ) {
            return true;
        }

        return false;
    }

    private function hasChildren()
    {
        $aliveSons = $this->data['heirs']['children']['aliveSons']['count'] ?? 0;
        $aliveDaughters = $this->data['heirs']['children']['aliveDaughters']['count'] ?? 0;
        $deceasedWithChildren = array_filter(
            array_merge(
                $this->data['heirs']['children']['deceasedSons']['names'] ?? [],
                $this->data['heirs']['children']['deceasedDaughters']['names'] ?? []
            ),
            fn($child) => ($child['sonsCount'] ?? 0) + ($child['daughtersCount'] ?? 0) > 0
        );
        return $aliveSons > 0 || $aliveDaughters > 0 || count($deceasedWithChildren) > 0;
    }

    private function hasDescendantSons()
    {
        $aliveSons = $this->data['heirs']['children']['aliveSons']['count'] ?? 0;
        $deceasedSonsWithChildren = array_filter(
            $this->data['heirs']['children']['deceasedSons']['names'] ?? [],
            fn($s) => ($s['sonsCount'] ?? 0) + ($s['daughtersCount'] ?? 0) > 0
        );
        return $aliveSons > 0 || count($deceasedSonsWithChildren) > 0;
    }


    private function hasDescendantDaughters()
    {
        $aliveDaughters = $this->data['heirs']['children']['aliveDaughters']['count'] ?? 0;
        $deceasedDaughtersWithChildren = array_filter(
            $this->data['heirs']['children']['deceasedDaughters']['names'] ?? [],
            fn($d) => ($d['sonsCount'] ?? 0) + ($d['daughtersCount'] ?? 0) > 0
        );
        return $aliveDaughters > 0 || count($deceasedDaughtersWithChildren) > 0;
    }

    private function hasAnySiblings()
    {
        $full = ($this->data['heirs']['siblings']['brothers']['count'] ?? 0) + ($this->data['heirs']['siblings']['sisters']['count'] ?? 0);
        $paternalHalf = ($this->data['heirs']['alivePaternalHalfSisters']['count'] ?? 0) + ($this->data['heirs']['otherRelatives']['paternalHalfBrother']['count'] ?? 0);
        $maternalHalf = ($this->data['heirs']['aliveMaternalHalfSiblings']['count'] ?? 0);
        return ($full + $paternalHalf + $maternalHalf) > 0;
    }

    private function isAlive($parentKey, $relation)
    {
        return isset($this->data['heirs'][$parentKey][$relation]) &&
            $this->data['heirs'][$parentKey][$relation]['status'] === 'alive';
    }

    private function calculateTotalEstate($assets)
    {
        // Convert Vue asset keys to proper labels
        $assetLabels = [
            'land' => 'জমির পরিমাণ',
            'flat' => 'ফ্ল্যাট',
            'cash' => 'নগদ টাকার পরিমাণ',
            'investment' => 'বিনিয়োগের পরিমাণ',
            'owedCash' => 'পাওনা টাকার পরিমাণ',
            'UnpaidDebt' => 'অপরিশোধিত ঋণ'
        ];

        $total = 0;
        foreach ($assets as $key => $asset) {
            if (array_key_exists($key, $assetLabels)) {
                $total += $asset['value'];
            }
        }
        return $total;
    }

    // Add this method to your class
    private function addOrdinalToLabel(string $baseLabel, int $index): string
    {
        $ordinals = [
            '১ম জন',
            '২য় জন',
            '৩য় জন',
            '৪র্থ জন',
            '৫ম জন',
            '৬ষ্ঠ জন',
            '৭ম জন',
            '৮ম জন',
            '৯ম জন',
            '১০ম জন',
            '১১তম জন',
            '১২তম জন',
            '১৩তম জন',
            '১৪তম জন',
            '১৫তম জন',
            '১৬তম জন',
            '১৭তম জন',
            '১৮তম জন',
            '১৯তম জন',
            '২০তম জন'
        ];

        $ordinal = $ordinals[$index] ?? ($index + 1) . 'তম জন';

        return "$baseLabel - $ordinal";
    }

    private function computeLCM($numbers)
    {
        $lcm = 1;
        foreach ($numbers as $number) {
            $lcm = ($lcm * $number) / $this->computeGCD($lcm, $number);
        }
        return $lcm;
    }

    private function computeGCD($a, $b)
    {
        while ($b != 0) {
            $temp = $a % $b;
            $a = $b;
            $b = $temp;
        }
        return $a;
    }

    private function addShare($relation, $numerator, $denominator, $name = null)
    {
        $this->results[] = [
            'relation' => $relation,
            'name' => $name,
            'numerator' => $numerator,
            'denominator' => $denominator,
            'share_amount' => $this->totalEstate * ($numerator / $denominator)
        ];
    }
}
