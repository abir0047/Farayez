<?php

namespace App\Services;

class InheritanceCalculator
{
    protected $data;
    protected $totalEstate;
    protected $results = [];
    protected $remainingShare = 1; // Track remaining share after fixed portions

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
                $assetShare = $asset['value'] * $share['share_fraction'];
                $assetDistribution[$assetKey]['shares'][] = [
                    'relation' => $share['relation'],
                    'name' => $share['name'],
                    'amount' => $assetShare,
                    'fraction' => $share['share_fraction']
                ];
            }
        }

        return [
            'total_estate' => $this->totalEstate,
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
                $this->addShare($this->data['heirs']['aliveParentStatus']['father']['label'], 1 / 6, $this->data['heirs']['aliveParentStatus']['father']['name']);
                $this->remainingShare -= 1 / 6;
            }
        } elseif ($this->isAlive('aliveGrandParentStatus', 'paternalGrandfather')) {
            // Paternal grandfather inherits only if father is dead
            $this->addShare($this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['label'], 1 / 6, $this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['name']);
            $this->remainingShare -= 1 / 6;
        }

        // 3. Mother's share (FIXED: Check for ANY full siblings, not "more than one")
        if ($this->isAlive('aliveParentStatus', 'mother')) {
            // Mother gets 1/6 if deceased has:
            // - Any descendants (sons/daughters) OR 
            // - ANY full siblings (even one)
            if ($this->hasDescendantSons() || $this->hasDescendantDaughters() || $this->hasAnySiblings()) {
                $this->addShare($this->data['heirs']['aliveParentStatus']['mother']['label'], 1 / 6, $this->data['heirs']['aliveParentStatus']['mother']['name']);
                $this->remainingShare -= 1 / 6;
            } else {
                // Otherwise, mother gets 1/3
                $this->addShare($this->data['heirs']['aliveParentStatus']['mother']['label'], 1 / 3, $this->data['heirs']['aliveParentStatus']['mother']['name']);
                $this->remainingShare -= 1 / 3;
            }
        }

        // 4. Daughter's share (correct)
        if (!$this->hasDescendantSons()) {
            $aliveDaughters = $this->data['heirs']['aliveDaughters']['count'] ?? 0;
            $deceasedDaughters = $this->data['heirs']['deceasedDaughters']['names'] ?? [];

            // Count deceased daughters with living children
            $deceasedWithChildren = array_filter($deceasedDaughters, function ($d) {
                return ($d['sonsCount'] ?? 0) > 0 || ($d['daughtersCount'] ?? 0) > 0;
            });

            $effectiveDaughters = $aliveDaughters + count($deceasedWithChildren);

            if ($effectiveDaughters === 1) {
                $totalDaughterShare = 1 / 2;
            } elseif ($effectiveDaughters > 1) {
                $totalDaughterShare = 2 / 3;
            } else {
                $totalDaughterShare = 0;
            }

            if ($totalDaughterShare > 0) {
                $this->remainingShare -= $totalDaughterShare;
                $perEffectiveDaughter = $totalDaughterShare / $effectiveDaughters;

                // Distribute to alive daughters
                foreach ($this->data['heirs']['aliveDaughters']['names'] as $daughter) {
                    $this->addShare($daughter['label'], $perEffectiveDaughter, $daughter['name']);
                }

                // Distribute to deceased daughters' children
                foreach ($deceasedWithChildren as $deceased) {
                    $totalParts = ($deceased['sonsCount'] * 2) + $deceased['daughtersCount'];

                    // Sons get 2:1 ratio
                    $sonShare = ($perEffectiveDaughter * 2) / $totalParts;
                    $daughterShare = $perEffectiveDaughter / $totalParts;

                    foreach ($deceased['sonsNames'] as $son) {
                        $this->addShare($son['label'], $sonShare, $son['name']);
                    }

                    foreach ($deceased['daughtersNames'] as $daughter) {
                        $this->addShare($daughter['label'], $daughterShare, $daughter['name']);
                    }
                }
            }
        }

        // 5. Full Sister's share (FIXED: Exclude if full brothers exist)
        if (!$this->hasDescendantSons() && !$this->hasDescendantDaughters() && !$this->isAlive('aliveParentStatus', 'father')) {
            // Check if there are NO full brothers
            $fullBrothersCount = $this->data['heirs']['siblings']['brothers']['count'] ?? 0;
            if ($fullBrothersCount === 0) {
                $fullSisterCount = $this->data['heirs']['siblings']['sisters']['count'] ?? 0;
                if ($fullSisterCount === 1) {
                    $this->addShare('Full Sister', 1 / 2);
                    $this->remainingShare -= 1 / 2;
                } elseif ($fullSisterCount > 1) {
                    $this->addShare('Full Sisters', 2 / 3);
                    $this->remainingShare -= 2 / 3;
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
            $paternalHalfSisterCount = $this->data['heirs']['alivePaternalHalfSisters']['count'] ?? 0;

            // Case 1: No full brothers, but there are full sisters
            if ($fullBrothersCount === 0 && $fullSistersCount >= 1) {
                if ($paternalHalfSisterCount >= 1) {
                    // Paternal half-sister(s) get 1/6 total (shared equally)
                    $share = 1 / 6;
                    $this->addShare('Paternal Half-Sister(s)', $share);
                    $this->remainingShare -= $share;
                }
            }
            // Case 2: No full siblings at all
            elseif ($fullBrothersCount === 0 && $fullSistersCount === 0) {
                if ($paternalHalfSisterCount === 1) {
                    $this->addShare('Paternal Half-Sister', 1 / 2);
                    $this->remainingShare -= 1 / 2;
                } elseif ($paternalHalfSisterCount > 1) {
                    $this->addShare('Paternal Half-Sisters', 2 / 3);
                    $this->remainingShare -= 2 / 3;
                }
            }
        }

        // 7. Maternal Half-Sibling's share (FIXED: Check for NO parents/children)
        if (!$this->isAlive('aliveParentStatus', 'father') && !$this->isAlive('aliveParentStatus', 'mother') && !$this->hasDescendantSons() && !$this->hasDescendantDaughters()) {
            $maternalHalfSiblingCount = $this->data['heirs']['aliveMaternalHalfSiblings']['count'] ?? 0;
            if ($maternalHalfSiblingCount === 1) {
                $this->addShare('Maternal Half-Sibling', 1 / 6);
                $this->remainingShare -= 1 / 6;
            } elseif ($maternalHalfSiblingCount > 1) {
                $this->addShare('Maternal Half-Siblings', 1 / 3);
                $this->remainingShare -= 1 / 3;
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
                $share = $hasChildren ? 1 / 8 : 1 / 4;
                $individualShare = $share / $wifeCount;
                foreach ($this->data['heirs']['spouseWives']['names'] as $wife) {
                    $this->addShare($wife['name'], $individualShare, $wife['name']);
                }
                $this->remainingShare -= $share;
            }
        } else {
            // Husband's share
            if ($this->data['heirs']['spouseStatus'] === 'alive') {
                $share = $hasChildren ? 1 / 4 : 1 / 2;
                $this->addShare('মৃত ব্যক্তির স্বামী', $share, $this->data['heirs']['spouseName']);
                $this->remainingShare -= $share;
            }
        }
    }


    private function allocateResidue()
    {
        if ($this->remainingShare > 0) {
            // 1. Descendant sons or their sons (already handled in distributeResidueAmongChildren)
            if ($this->hasDescendantSons()) {
                $this->distributeResidueAmongChildren();
            }
            // 2. Father
            elseif ($this->isAlive('aliveParentStatus', 'father')) {
                $this->addShare($this->data['heirs']['aliveParentStatus']['father']['label'], $this->remainingShare, $this->data['heirs']['aliveParentStatus']['father']['name']);
                $this->remainingShare = 0;
            }
            // 3. Paternal grandfather
            elseif ($this->isAlive('aliveGrandParentStatus', 'paternalGrandfather')) {
                $this->addShare($this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['label'], $this->remainingShare, $this->data['heirs']['aliveGrandParentStatus']['paternalGrandfather']['name']);
                $this->remainingShare = 0;
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
            // // 7. Half paternal uncles (same grandfather) or their sons
            elseif ($this->hasHalfPaternalUnclesOrDescendants()) {
                $this->distributeResidueToHalfPaternalUncles();
            } else {
                // Handle cousins or other agnatic heirs if needed
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

        // Calculate shares based on 2:1 ratio
        $totalParts = ($effectiveSons * 2) + $effectiveDaughters;
        $partValue = $this->remainingShare / $totalParts;

        // Distribute to alive sons (2 parts each)
        foreach ($this->data['heirs']['children']['aliveSons']['names'] as $son) {
            $this->addShare($this->data['heirs']['children']['aliveSons']['label'], $partValue * 2, $son['name']);
        }

        // Distribute to alive daughters (1 part each)
        foreach ($this->data['heirs']['children']['aliveDaughters']['names'] as $daughter) {
            $this->addShare($this->data['heirs']['children']['aliveDaughters']['label'], $partValue, $daughter['name']);
        }

        // Distribute to deceased sons' children
        foreach ($deceasedSons as $deceasedSon) {
            $totalChildParts = ($deceasedSon['sonsCount'] * 2) + $deceasedSon['daughtersCount'];
            if ($totalChildParts === 0) continue;

            $deceasedSonShare = $partValue * 2; // 2 parts for each deceased son

            // Sons of deceased son
            foreach ($deceasedSon['sonsNames'] as $grandson) {
                $share = ($deceasedSonShare * 2) / $totalChildParts;
                $this->addShare($grandson['label'], $share, $grandson['name']);
            }

            // Daughters of deceased son
            foreach ($deceasedSon['daughtersNames'] as $granddaughter) {
                $share = $deceasedSonShare / $totalChildParts;
                $this->addShare($granddaughter['label'], $share, $granddaughter['name']);
            }
        }

        // Distribute to deceased daughters' children
        foreach ($deceasedDaughters as $deceasedDaughter) {
            $totalChildParts = ($deceasedDaughter['sonsCount'] * 2) + $deceasedDaughter['daughtersCount'];
            if ($totalChildParts === 0) continue;

            // Deceased daughter’s share (1 part, as daughters get 1 part in residue)
            $deceasedDaughterShare = $partValue * 1;

            // Sons of deceased daughter (2:1 ratio)
            foreach ($deceasedDaughter['sonsNames'] as $grandson) {
                $share = ($deceasedDaughterShare * 2) / $totalChildParts;
                $this->addShare($grandson['label'], $share, $grandson['name']);
            }

            // Daughters of deceased daughter
            foreach ($deceasedDaughter['daughtersNames'] as $granddaughter) {
                $share = $deceasedDaughterShare / $totalChildParts;
                $this->addShare($granddaughter['label'], $share, $granddaughter['name']);
            }
        }
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
        // Alive brothers/sisters
        $aliveBrothers = $this->data['heirs']['siblings']['brothers']['count'] ?? 0;
        $aliveSisters = $this->data['heirs']['siblings']['sisters']['count'] ?? 0;

        // Deceased brothers with sons (assuming data structure supports this)
        $deceasedBrothersWithSons = array_filter(
            $this->data['heirs']['siblings']['deceasedBrothers'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        $effectiveBrothers = $aliveBrothers + count($deceasedBrothersWithSons);
        $effectiveSisters = $aliveSisters;

        $totalParts = ($effectiveBrothers * 2) + $effectiveSisters;
        $partValue = $this->remainingShare / $totalParts;

        // Alive brothers
        foreach ($this->data['heirs']['siblings']['brothers']['names'] as $brother) {
            $this->addShare($brother['label'], $partValue * 2, $brother['name']);
        }

        // Deceased brothers' sons (inherit their father’s share)
        foreach ($deceasedBrothersWithSons as $deceasedBrother) {
            $totalSonsParts = $deceasedBrother['sonsCount'] * 2;
            $sharePerSon = ($partValue * 2) / $totalSonsParts;
            foreach ($deceasedBrother['sonsNames'] as $son) {
                $this->addShare($son['label'], $sharePerSon, $son['name']);
            }
        }

        // Alive sisters
        foreach ($this->data['heirs']['siblings']['sisters']['names'] as $sister) {
            $this->addShare($sister['label'], $partValue, $sister['name']);
        }

        $this->remainingShare = 0;
    }

    private function hasPaternalHalfSiblingsOrDescendants()
    {
        $halfBrothers = $this->data['heirs']['otherRelatives']['paternalHalfBrother']['count'] ?? 0;
        $halfSisters = $this->data['heirs']['otherRelatives']['paternalHalfSister']['count'] ?? 0;

        // Check deceased half-brothers with sons
        $deceasedHalfBrothersWithSons = array_filter(
            $this->data['heirs']['otherRelatives']['deceasedPaternalHalfBrothers'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        return $halfBrothers + count($deceasedHalfBrothersWithSons) + $halfSisters > 0;
    }

    private function distributeResidueToPaternalHalfSiblings()
    {
        $aliveHalfBrothers = $this->data['heirs']['otherRelatives']['paternalHalfBrother']['count'] ?? 0;
        $aliveHalfSisters = $this->data['heirs']['otherRelatives']['paternalHalfSister']['count'] ?? 0;

        $deceasedHalfBrothersWithSons = array_filter(
            $this->data['heirs']['otherRelatives']['deceasedPaternalHalfBrothers'] ?? [],
            fn($brother) => ($brother['sonsCount'] ?? 0) > 0
        );

        $effectiveHalfBrothers = $aliveHalfBrothers + count($deceasedHalfBrothersWithSons);
        $effectiveHalfSisters = $aliveHalfSisters;

        $totalParts = ($effectiveHalfBrothers * 2) + $effectiveHalfSisters;
        $partValue = $this->remainingShare / $totalParts;

        // Alive paternal half-brothers
        foreach ($this->data['heirs']['otherRelatives']['paternalHalfBrother']['names'] as $brother) {
            $this->addShare($brother['label'], $partValue * 2, $brother['name']);
        }

        // Deceased half-brothers' sons
        foreach ($deceasedHalfBrothersWithSons as $deceasedBrother) {
            $totalSonsParts = $deceasedBrother['sonsCount'] * 2;
            $sharePerSon = ($partValue * 2) / $totalSonsParts;
            foreach ($deceasedBrother['sonsNames'] as $son) {
                $this->addShare($son['label'], $sharePerSon, $son['name']);
            }
        }

        // Alive paternal half-sisters
        foreach ($this->data['heirs']['otherRelatives']['paternalHalfSister']['names'] as $sister) {
            $this->addShare($sister['label'], $partValue, $sister['name']);
        }

        $this->remainingShare = 0;
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
        $remaining = $this->remainingShare;

        // Case 1: Alive paternal uncles
        if (($paternalUncle['count'] ?? 0) > 0) {
            $aliveUncles = $paternalUncle['count'];
            $sharePerUncle = $remaining / $aliveUncles;
            foreach ($paternalUncle['names'] as $uncle) {
                $this->addShare($uncle['label'], $sharePerUncle, $uncle['name']);
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 2: Deceased uncles with living sons
        if (($paternalUncle['hasSons'] ?? 'no') === 'yes' && ($paternalUncle['sonsCount'] ?? 0) > 0) {
            $sonsCount = $paternalUncle['sonsCount'];
            $sharePerSon = $remaining / $sonsCount;
            foreach ($paternalUncle['sonsNames'] as $son) {
                $this->addShare($son['label'], $sharePerSon, $son['name']);
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 3: Deceased uncles' grandsons
        if (($paternalUncle['hasGrandsons'] ?? 'no') === 'yes' && ($paternalUncle['grandsonsCount'] ?? 0) > 0) {
            $grandsonsCount = $paternalUncle['grandsonsCount'];
            $sharePerGrandson = $remaining / $grandsonsCount;
            foreach ($paternalUncle['grandsonsNames'] as $grandson) {
                $this->addShare($grandson['label'], $sharePerGrandson, $grandson['name']);
            }
            $this->remainingShare = 0;
            return;
        }
    }
    private function distributeResidueToHalfPaternalUncles()
    {
        $halfUncle = $this->data['heirs']['otherRelatives']['paternalHalfUncle'] ?? [];
        $remaining = $this->remainingShare;

        // Case 1: Alive half-uncles
        if (($halfUncle['count'] ?? 0) > 0) {
            $aliveUncles = $halfUncle['count'];
            $sharePerUncle = $remaining / $aliveUncles;
            foreach ($halfUncle['names'] as $uncle) {
                $this->addShare($uncle['label'], $sharePerUncle, $uncle['name']);
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 2: Deceased half-uncles with sons
        if (($halfUncle['hasSons'] ?? 'no') === 'yes' && ($halfUncle['sonsCount'] ?? 0) > 0) {
            $sonsCount = $halfUncle['sonsCount'];
            $sharePerSon = $remaining / $sonsCount;
            foreach ($halfUncle['sonsNames'] as $son) {
                $this->addShare($son['label'], $sharePerSon, $son['name']);
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 3: Deceased half-uncles' grandsons
        if (($halfUncle['hasGrandsons'] ?? 'no') === 'yes' && ($halfUncle['grandsonsCount'] ?? 0) > 0) {
            $grandsonsCount = $halfUncle['grandsonsCount'];
            $sharePerGrandson = $remaining / $grandsonsCount;
            foreach ($halfUncle['grandsonsNames'] as $grandson) {
                $this->addShare($grandson['label'], $sharePerGrandson, $grandson['name']);
            }
            $this->remainingShare = 0;
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

    private function addShare($relation, $fraction, $name = null)
    {
        $this->results[] = [
            'relation' => $relation,
            'name' => $name,
            'share_fraction' => $fraction,
            'share_amount' => $this->totalEstate * $fraction
        ];
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

    private function hasFullSiblingsMoreThanOne()
    {
        $fullBrothers = $this->data['heirs']['siblings']['brothers']['count'] ?? 0;
        $fullSisters = $this->data['heirs']['siblings']['sisters']['count'] ?? 0;

        return ($fullBrothers + $fullSisters) > 1;
    }
    private function hasFullSisters()
    {
        return ($this->data['heirs']['siblings']['sisters']['count'] ?? 0) > 0;
    }
    private function hasFullSiblings()
    {
        return ($this->data['heirs']['siblings']['brothers']['count'] ?? 0) > 0 ||
            ($this->data['heirs']['siblings']['sisters']['count'] ?? 0) > 0;
    }
    private function hasAnySiblings()
    {
        $full = ($this->data['heirs']['siblings']['brothers']['count'] ?? 0) + ($this->data['heirs']['siblings']['sisters']['count'] ?? 0);
        $paternalHalf = ($this->data['heirs']['alivePaternalHalfSisters']['count'] ?? 0) + ($this->data['heirs']['otherRelatives']['paternalHalfBrother']['count'] ?? 0);
        $maternalHalf = ($this->data['heirs']['aliveMaternalHalfSiblings']['count'] ?? 0);
        return ($full + $paternalHalf + $maternalHalf) > 0;
    }

    private function hasMaleLineAncestorsOrDescendants()
    {
        return $this->hasDescendantSons() ||
            $this->isAlive('aliveParentStatus', 'father') ||
            $this->isAlive('aliveGrandParentStatus', 'paternalGrandfather');
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
}
