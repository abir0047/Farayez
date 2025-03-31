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
            $aliveDaughters = $this->data['heirs']['children']['aliveDaughters']['count'] ?? 0;
            $deceasedDaughters = $this->data['heirs']['children']['deceasedDaughters']['names'] ?? [];

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
                foreach ($this->data['heirs']['children']['aliveDaughters']['names'] as $index => $daughter) {
                    $label = $this->addOrdinalToLabel($this->data['heirs']['children']['aliveDaughters']['label'], $index);
                    $this->addShare($label, $perEffectiveDaughter, $daughter['name']);
                }

                foreach ($deceasedWithChildren as $deceasedIndex => $deceased) {
                    $totalParts = ($deceased['sonsCount'] * 2) + $deceased['daughtersCount'];

                    $sonShare = ($perEffectiveDaughter * 2) / $totalParts;
                    $daughterShare = $perEffectiveDaughter / $totalParts;

                    // For sons of deceased daughter
                    foreach ($deceased['sonsNames'] as $sonIndex => $son) {
                        $label = $this->addOrdinalToLabel(
                            "মৃত মেয়ের ছেলে",  // Base label
                            $sonIndex           // Index within this daughter's sons
                        );
                        $this->addShare($label, $sonShare, $son['name'] ?? null);
                    }

                    // For daughters of deceased daughter
                    foreach ($deceased['daughtersNames'] as $daughterIndex => $daughter) {
                        $label = $this->addOrdinalToLabel(
                            "মৃত মেয়ের মেয়ে",  // Base label
                            $daughterIndex      // Index within this daughter's daughters
                        );
                        $this->addShare($label, $daughterShare, $daughter['name'] ?? null);
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
                    // Single sister - use name if available
                    $sister = $sisterNames[0] ?? [];
                    $label = $this->addOrdinalToLabel(
                        $sisters['label'] ?? 'সহোদর বোন',
                        0
                    );
                    $this->addShare(
                        $label,
                        1 / 2,
                        $sister['name'] ?? null
                    );
                    $this->remainingShare -= 1 / 2;
                } elseif ($fullSisterCount > 1) {
                    // Multiple sisters - divide 2/3 equally
                    $sharePerSister = (2 / 3) / $fullSisterCount;

                    foreach ($sisterNames as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $sisters['label'] ?? 'সহোদর বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $sharePerSister,
                            $sister['name'] ?? null
                        );
                    }
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
            $paternalHalfSisters = $this->data['heirs']['alivePaternalHalfSisters'] ?? [];
            $paternalHalfSisterCount = $paternalHalfSisters['count'] ?? 0;
            $paternalHalfSisterNames = $paternalHalfSisters['names'] ?? [];

            // Case 1: No full brothers, but full sisters exist
            if ($fullBrothersCount === 0 && $fullSistersCount >= 1) {
                if ($paternalHalfSisterCount >= 1) {
                    $share = 1 / 6;
                    $sharePerSister = $share / $paternalHalfSisterCount;

                    foreach ($paternalHalfSisterNames as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $paternalHalfSisters['label'] ?? 'বৈমাতৃয় বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $sharePerSister,
                            $sister['name'] ?? null
                        );
                    }
                    $this->remainingShare -= $share;
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
                        1 / 2,
                        $sister['name'] ?? null
                    );
                    $this->remainingShare -= 1 / 2;
                } elseif ($paternalHalfSisterCount > 1) {
                    $share = 2 / 3;
                    $sharePerSister = $share / $paternalHalfSisterCount;

                    foreach ($paternalHalfSisterNames as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $paternalHalfSisters['label'] ?? 'বৈমাতৃয় বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $sharePerSister,
                            $sister['name'] ?? null
                        );
                    }
                    $this->remainingShare -= 2 / 3;
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
            // Get from correct data path (otherRelatives instead of aliveMaternalHalfSiblings)
            $maternalBrothers = $this->data['heirs']['otherRelatives']['maternalHalfBrother'] ?? [];
            $maternalSisters = $this->data['heirs']['otherRelatives']['maternalHalfSister'] ?? [];

            $brotherCount = $maternalBrothers['count'] ?? 0;
            $sisterCount = $maternalSisters['count'] ?? 0;
            $totalSiblings = $brotherCount + $sisterCount;

            if ($totalSiblings > 0) {
                // Determine total share (1/6 for single, 1/3 for multiple)
                $totalShare = $totalSiblings === 1 ? 1 / 6 : 1 / 3;
                $totalParts = ($brotherCount * 2) + $sisterCount;

                if ($totalParts > 0) {
                    $partValue = $totalShare / $totalParts;

                    // Process brothers with 2:1 ratio
                    foreach ($maternalBrothers['names'] as $index => $brother) {
                        $label = $this->addOrdinalToLabel(
                            $maternalBrothers['label'] ?? 'বৈপিত্রেয় ভাই',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $partValue * 2,
                            $brother['name'] ?? null
                        );
                    }

                    // Process sisters
                    foreach ($maternalSisters['names'] as $index => $sister) {
                        $label = $this->addOrdinalToLabel(
                            $maternalSisters['label'] ?? 'বৈপিত্রেয় বোন',
                            $index
                        );
                        $this->addShare(
                            $label,
                            $partValue * 1,
                            $sister['name'] ?? null
                        );
                    }

                    $this->remainingShare -= $totalShare;
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
                $share = $hasChildren ? 1 / 8 : 1 / 4;
                $individualShare = $share / $wifeCount;
                foreach ($this->data['heirs']['spouseWives']['names'] as $index => $wife) {
                    $label = $this->addOrdinalToLabel("মৃত ব্যক্তির জীবিত স্ত্রী", $index);
                    $this->addShare($label, $individualShare, $wife['name']);
                }
                $this->remainingShare -= $share;
            }
        } else {
            // Husband's share
            if ($this->data['heirs']['spouseStatus'] === 'alive') {
                $share = $hasChildren ? 1 / 4 : 1 / 2;
                $this->addShare('মৃত ব্যক্তির জীবিত স্বামী', $share, $this->data['heirs']['spouseName']);
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
        foreach ($this->data['heirs']['children']['aliveSons']['names'] as $index => $son) {
            $label = $this->addOrdinalToLabel($this->data['heirs']['children']['aliveSons']['label'], $index);
            $this->addShare($label, $partValue * 2, $son['name']);
        }

        // Distribute to alive daughters (1 part each)
        foreach ($this->data['heirs']['children']['aliveDaughters']['names'] as $index => $daughter) {
            $label = $this->addOrdinalToLabel($this->data['heirs']['children']['aliveDaughters']['label'], $index);
            $this->addShare($label, $partValue, $daughter['name']);
        }

        // Distribute to deceased sons' children
        foreach ($deceasedSons as $deceasedSon) {
            $totalChildParts = ($deceasedSon['sonsCount'] * 2) + $deceasedSon['daughtersCount'];
            if ($totalChildParts === 0) continue;

            $deceasedSonShare = $partValue * 2;

            // Sons of deceased son
            foreach ($deceasedSon['sonsNames'] as $sonIndex => $grandson) {
                $share = ($deceasedSonShare * 2) / $totalChildParts;
                $label = $this->addOrdinalToLabel("মৃত ছেলের ছেলে", $sonIndex);
                $this->addShare($label, $share, $grandson['name'] ?? null);
            }

            // Daughters of deceased son
            foreach ($deceasedSon['daughtersNames'] as $daughterIndex => $granddaughter) {
                $share = $deceasedSonShare / $totalChildParts;
                $label = $this->addOrdinalToLabel("মৃত ছেলের মেয়ে", $daughterIndex);
                $this->addShare($label, $share, $granddaughter['name'] ?? null);
            }
        }

        // Distribute to deceased daughters' children
        foreach ($deceasedDaughters as $deceasedDaughter) {
            $totalChildParts = ($deceasedDaughter['sonsCount'] * 2) + $deceasedDaughter['daughtersCount'];
            if ($totalChildParts === 0) continue;

            $deceasedDaughterShare = $partValue * 1;

            // Sons of deceased daughter
            foreach ($deceasedDaughter['sonsNames'] as $sonIndex => $grandson) {
                $share = ($deceasedDaughterShare * 2) / $totalChildParts;
                $label = $this->addOrdinalToLabel("মৃত মেয়ের ছেলে", $sonIndex);
                $this->addShare($label, $share, $grandson['name'] ?? null);
            }

            // Daughters of deceased daughter
            foreach ($deceasedDaughter['daughtersNames'] as $daughterIndex => $granddaughter) {
                $share = $deceasedDaughterShare / $totalChildParts;
                $label = $this->addOrdinalToLabel("মৃত মেয়ের মেয়ে", $daughterIndex);
                $this->addShare($label, $share, $granddaughter['name'] ?? null);
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
        $partValue = $this->remainingShare / $totalParts;

        // Alive brothers
        foreach (array_slice($brothersData['names'] ?? [], 0, $aliveBrothersCount) as $index => $brother) {
            $label = $this->addOrdinalToLabel(
                $brothersData['label'] ?? 'সহোদর ভাই',
                $index
            );
            $this->addShare(
                $label,
                $partValue * 2,
                $brother['name'] ?? null
            );
        }

        // Deceased brothers' sons
        foreach ($deceasedBrothersWithSons as $deceasedBrother) {
            $sonsCount = $deceasedBrother['sonsCount'] ?? 0;
            $totalSonsParts = $sonsCount * 2;

            if ($totalSonsParts > 0) {
                $sharePerSon = ($partValue * 2) / $totalSonsParts;

                foreach (array_slice($deceasedBrother['sonsNames'] ?? [], 0, $sonsCount) as $sonIndex => $son) {
                    $label = $this->addOrdinalToLabel(
                        'মৃত ভাইয়ের ছেলে',
                        $sonIndex
                    );
                    $this->addShare(
                        $label,
                        $sharePerSon,
                        $son['name'] ?? null
                    );
                }
            }
        }

        // Alive sisters
        foreach (array_slice($sistersData['names'] ?? [], 0, $aliveSistersCount) as $index => $sister) {
            $label = $this->addOrdinalToLabel(
                $sistersData['label'] ?? 'সহোদর বোন',
                $index
            );
            $this->addShare(
                $label,
                $partValue,
                $sister['name'] ?? null
            );
        }

        $this->remainingShare = 0;
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
        $partValue = $this->remainingShare / $totalParts;

        // Alive paternal half-brothers
        foreach (array_slice($halfBrotherData['names'] ?? [], 0, $aliveHalfBrothersCount) as $index => $brother) {
            $label = $this->addOrdinalToLabel(
                $halfBrotherData['label'] ?? 'বৈমাতৃয় ভাই',
                $index
            );
            $this->addShare(
                $label,
                $partValue * 2,
                $brother['name'] ?? null
            );
        }

        // Deceased half-brothers' sons
        foreach ($deceasedHalfBrothersWithSons as $deceasedBrother) {
            $sonsCount = $deceasedBrother['sonsCount'] ?? 0;
            $totalSonsParts = $sonsCount * 2;

            if ($totalSonsParts > 0) {
                $sharePerSon = ($partValue * 2) / $totalSonsParts;

                foreach (array_slice($deceasedBrother['sonsNames'] ?? [], 0, $sonsCount) as $sonIndex => $son) {
                    $label = $this->addOrdinalToLabel(
                        'মৃত বৈমাতৃয় ভাইয়ের ছেলে',
                        $sonIndex
                    );
                    $this->addShare(
                        $label,
                        $sharePerSon,
                        $son['name'] ?? null
                    );
                }
            }
        }

        // Alive paternal half-sisters
        foreach (array_slice($halfSisterData['names'] ?? [], 0, $aliveHalfSistersCount) as $index => $sister) {
            $label = $this->addOrdinalToLabel(
                $halfSisterData['label'] ?? 'বৈমাতৃয় বোন',
                $index
            );
            $this->addShare(
                $label,
                $partValue,
                $sister['name'] ?? null
            );
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
        $aliveCount = $paternalUncle['count'] ?? 0;
        if ($aliveCount > 0) {
            $sharePerUncle = $remaining / $aliveCount;
            foreach (array_slice($paternalUncle['names'] ?? [], 0, $aliveCount) as $index => $uncle) {
                $label = $this->addOrdinalToLabel(
                    $paternalUncle['label'] ?? 'চাচা',
                    $index
                );
                $this->addShare(
                    $label,
                    $sharePerUncle,
                    $uncle['name'] ?? null
                );
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 2: Deceased uncles with living sons
        $sonsCount = $paternalUncle['sonsCount'] ?? 0;
        if (($paternalUncle['hasSons'] ?? 'no') === 'yes' && $sonsCount > 0) {
            $sharePerSon = $remaining / $sonsCount;
            foreach (array_slice($paternalUncle['sonsNames'] ?? [], 0, $sonsCount) as $index => $son) {
                $label = $this->addOrdinalToLabel(
                    'মৃত চাচার ছেলে',
                    $index
                );
                $this->addShare(
                    $label,
                    $sharePerSon,
                    $son['name'] ?? null
                );
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 3: Deceased uncles' grandsons
        $grandsonsCount = $paternalUncle['grandsonsCount'] ?? 0;
        if (($paternalUncle['hasGrandsons'] ?? 'no') === 'yes' && $grandsonsCount > 0) {
            $sharePerGrandson = $remaining / $grandsonsCount;
            foreach (array_slice($paternalUncle['grandsonsNames'] ?? [], 0, $grandsonsCount) as $index => $grandson) {
                $label = $this->addOrdinalToLabel(
                    'মৃত চাচার নাতি',
                    $index
                );
                $this->addShare(
                    $label,
                    $sharePerGrandson,
                    $grandson['name'] ?? null
                );
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
        $aliveCount = $halfUncle['count'] ?? 0;
        if ($aliveCount > 0) {
            $sharePerUncle = $remaining / $aliveCount;
            foreach (array_slice($halfUncle['names'] ?? [], 0, $aliveCount) as $index => $uncle) {
                $label = $this->addOrdinalToLabel(
                    $halfUncle['label'] ?? 'বৈমাতৃয় চাচা',
                    $index
                );
                $this->addShare(
                    $label,
                    $sharePerUncle,
                    $uncle['name'] ?? null
                );
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 2: Deceased half-uncles with sons
        $sonsCount = $halfUncle['sonsCount'] ?? 0;
        if (($halfUncle['hasSons'] ?? 'no') === 'yes' && $sonsCount > 0) {
            $sharePerSon = $remaining / $sonsCount;
            foreach (array_slice($halfUncle['sonsNames'] ?? [], 0, $sonsCount) as $index => $son) {
                $label = $this->addOrdinalToLabel(
                    'মৃত বৈমাতৃয় চাচার ছেলে',
                    $index
                );
                $this->addShare(
                    $label,
                    $sharePerSon,
                    $son['name'] ?? null
                );
            }
            $this->remainingShare = 0;
            return;
        }

        // Case 3: Deceased half-uncles' grandsons
        $grandsonsCount = $halfUncle['grandsonsCount'] ?? 0;
        if (($halfUncle['hasGrandsons'] ?? 'no') === 'yes' && $grandsonsCount > 0) {
            $sharePerGrandson = $remaining / $grandsonsCount;
            foreach (array_slice($halfUncle['grandsonsNames'] ?? [], 0, $grandsonsCount) as $index => $grandson) {
                $label = $this->addOrdinalToLabel(
                    'মৃত বৈমাতৃয় চাচার নাতি',
                    $index
                );
                $this->addShare(
                    $label,
                    $sharePerGrandson,
                    $grandson['name'] ?? null
                );
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
}
