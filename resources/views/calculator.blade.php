@extends('layouts.app')

@section('content')
    <!-- Main Calculator Container -->
    <div id="calculator"
        class="w-full max-w-4xl lg:w-[1024px] lg:max-w-none mx-auto p-4 md:p-6 bg-white shadow-lg rounded-lg border border-[#006F45] md:my-4"
        data-initial="{{ json_encode($initialData ?? new stdClass()) }}">
        <h2 class="text-lg md:text-xl font-bold text-center mb-6 text-[#006F45]">
            মুসলিম উত্তরাধিকার আইন অনুযায়ী সম্পত্তি বন্টন
        </h2>
        <!-- Tab Navigation Buttons -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-6">
            <div v-for="(btn, index) in buttons" :key="index" @click="activeTab = index"
                class="p-2 md:p-3 text-xs md:text-sm text-center font-medium rounded-lg transition-all cursor-pointer hover:bg-[#03442C] hover:text-white"
                :class="activeTab === index ? 'bg-[#006F45] text-white shadow-md' :
                    'bg-gray-100 text-black border border-gray-300 hover:border-[#006F45]'">
                @{{ btn.label }}
            </div>
        </div>
        <!-- Tab Content Container -->
        <div class="p-3 md:p-4 bg-white rounded-lg">
            <!-- Tab 0: Deceased Info Input-->
            <template v-if="activeTab === 0">
                <!-- Deceased Info Tab: Name, Gender, Death Date, Marital Status -->
                <div class="grid grid-cols-1 gap-4">
                    <!-- Name Input -->
                    <div class="space-y-2">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName('মৃত ব্যক্তির নাম') }}:</label>
                            <input type="text" v-model="formData.deceasedInfo.name"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                        </div>
                        <!-- Gender Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName('মৃত ব্যক্তির লিঙ্গ') }}:</label>
                            <div class="border border-gray-300 rounded-lg p-3 md:p-4 bg-white">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <label class="flex items-center space-x-3 cursor-pointer group">
                                        <input type="radio" value="male" v-model="formData.deceasedInfo.gender"
                                            class="h-5 w-5 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-0 focus:border-[#006F45] rounded-full transition-all">
                                        <span
                                            class="text-black group-hover:text-[#006F45] text-sm md:text-base">পুরুষ</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer group">
                                        <input type="radio" value="female" v-model="formData.deceasedInfo.gender"
                                            class="h-5 w-5 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-0 focus:border-[#006F45] rounded-full transition-all">
                                        <span class="text-black group-hover:text-[#006F45] text-sm md:text-base">নারী</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Deceased Person's Death Date Input -->
                        <!-- Death Date Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">মৃত্যুর তারিখ:</label>
                            <input type="date" v-model="formData.deceasedInfo.deathDate"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                        </div>
                        <!-- Death Time Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">মৃত্যুর সময়:</label>
                            <input type="time" v-model="formData.deceasedInfo.deathTime"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                        </div>
                        <!-- Marital Status Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">বৈবাহিক অবস্থা:</label>
                            <select v-model="formData.deceasedInfo.maritalStatus"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200 bengali-text">
                                <option value="married">বিবাহিত</option>
                                <option value="unmarried">অবিবাহিত</option>
                                <option value="divorced">অবিবাহিত কিন্তু তালাকপ্রাপ্ত</option>
                            </select>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Tab 1: Assets Input-->
            <template v-if="activeTab === 1">
                <!-- Asset Entry Tab: v-for over assets -->
                <div class="grid grid-cols-1 gap-4">
                    <div v-for="(field, key) in formData.assets" :key="key" class="space-y-3">
                        <!-- Asset Label and Placeholder -->
                        <label class="block text-sm font-semibold text-[#006F45]">@{{ field.label }}:</label>
                        <span class="text-sm text-gray-600">(@{{ field.placeholder }})</span>

                        <!-- Hint Text - Only show when input is focused -->
                        <div v-if="field.showHint"
                            class="bg-green-50 border-l-4 border-[#006F45] p-3 rounded-r-lg transition-all duration-300">
                            <p class="text-sm text-[#006F45] leading-relaxed">@{{ field.hint }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Asset Value Input -->
                            <input type="text" v-model="field.value" @input="convertToBengali($event, field)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200"
                                :placeholder="field.placeholder" @focus="handleFocus($event, field)"
                                @blur="handleBlur($event, field)">
                        </div>
                    </div>
                </div>
            </template>
            <!-- Tab 2: Heirs & Relatives Input-->

            <template v-if="activeTab === 2">
                <!-- Heirs and Relatives Main Section -->
                <div class="space-y-6">
                    <div class="bg-[#F5FFE8] border-l-4 border-[#006F45] p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-[#006F45]">
                            <span class="font-semibold block mb-2">
                                <span v-if="formData.deceasedInfo.deathDate">
                                    মৃত্যুর তারিখ ও সময়: @{{ formatDate(formData.deceasedInfo.deathDate) }} @{{ formatTime(formData.deceasedInfo.deathTime) }}
                                </span>
                                <span v-else class="text-red-600">(মৃত্যুর তারিখ ও সময় প্রদান করুন)</span>
                            </span>
                            <span class="block text-[#03442C] mb-2">দয়া করে ওয়ারিশদের তথ্য প্রদান করুন যারা এই তারিখ ও সময়ে
                                জীবিত ছিলেন।</span>
                            <span class="text-red-600 block">মনে রাখবেন: যদি কেউ এই সময়ের মাত্র ১ মিনিট পরে মারা গিয়ে থাকেন,
                                তবুও তাকে ওয়ারিশ হিসেবে গণ্য করতে হবে।</span>
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="(relation, key) in formData.heirs.aliveParentStatus" :key="key"
                            class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex flex-col md:flex-row items-start md:items-center gap-2 mb-3">
                                <label class="block text-sm font-semibold text-[#006F45]">@{{ parentLabels[key] }}:</label>
                                <div class="flex gap-3">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="alive" v-model="relation.status"
                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                        <span class="text-black">জীবিত</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="dead" v-model="relation.status"
                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                        <span class="text-black">মৃত</span>
                                    </label>
                                </div>
                            </div>
                            <div v-if="relation.status === 'alive'" class="w-full mt-2">
                                <input type="text" v-model="relation.name" :placeholder="`${parentLabels[key]}-এর নাম`"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                            </div>
                        </div>
                    </div>
                    <!-- Spouse Section: Input for wives (if deceased is male) or husband (if deceased is female) -->
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                            <label class="w-full md:w-1/3 text-sm font-semibold text-[#006F45]">
                                @{{ formData.deceasedInfo.gender === 'male' ? replaceDeceasedName('মৃত ব্যক্তির বর্তমানে জীবিত স্ত্রীর সংখ্যা') : replaceDeceasedName('মৃত ব্যক্তির স্বামীর অবস্থা') }}:
                            </label>
                            <div v-if="formData.deceasedInfo.gender === 'male'" class="w-full md:w-2/3 space-y-3">
                                <select v-model="formData.heirs.spouseWives.count"
                                    :disabled="formData.deceasedInfo.maritalStatus !== 'married'"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <option value="0">স্ত্রী নেই</option>
                                    <option value="1">১ম স্ত্রী</option>
                                    <option value="2">২য় স্ত্রী</option>
                                    <option value="3">৩য় স্ত্রী</option>
                                    <option value="4">৪র্থ স্ত্রী</option>
                                </select>
                                <div v-for="(wife, index) in formData.heirs.spouseWives.names" :key="index"
                                    class="space-y-2">
                                    <input type="text" v-model="wife.name"
                                        :placeholder="`${getBengaliOrdinal(index + 1)} স্ত্রীর নাম`"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                </div>
                            </div>
                            <div v-else class="w-full md:w-2/3 space-y-3">
                                <div class="flex gap-4">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="alive" v-model="formData.heirs.spouseStatus"
                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                        <span class="text-black">জীবিত</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="dead" v-model="formData.heirs.spouseStatus"
                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                        <span class="text-black">মৃত</span>
                                    </label>
                                </div>
                                <div v-if="formData.heirs.spouseStatus === 'alive'">
                                    <input type="text" v-model="formData.heirs.spouseName" placeholder="স্বামীর নাম"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template v-for="(child, key) in formData.heirs.children">
                                <div v-if="!['deceasedSons', 'deceasedDaughters'].includes(key)" :key="key"
                                    class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-3">
                                        <label
                                            class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(child.label) }}:</label>
                                        <select v-model="child.count" @change="updateNames(child)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                            <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, key) }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <!-- v-for: Input for each alive child's name -->
                                        <div v-for="(member, index) in child.names" :key="index">
                                            <input type="text" v-model="member.name"
                                                :placeholder="`${replaceDeceasedName(child.label)} ${getBengaliOrdinal(index + 1)} এর নাম`"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <!--
                                                                            Deceased Sons Section:
                                                                            This section captures the number of deceased sons of the deceased person, their names, and their children (i.e., the deceased's grandchildren through sons).

                                                                            Islamic inheritance law considers the children of deceased sons (grandchildren) as eligible heirs if their father (the son of the deceased) died before the deceased. For each deceased son, the user must input:
                                                                            - The son's name (for clarity in distribution and for descendant tracking)
                                                                            - The number and names of his sons (grandsons)
                                                                            - The number and names of his daughters (granddaughters)
                                                                            This allows the inheritance logic to properly allocate shares to grandchildren as per the rules.
                                                                        -->
                        <div class="space-y-6">
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="mb-4 pb-2 border-b border-gray-200">
                                    <h3 class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName('মৃত ব্যক্তির মৃত ছেলে') }}</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-4">
                                        <label class="text-sm font-medium text-black">মৃত ছেলের সংখ্যা:</label>
                                        <select v-model="formData.heirs.children.deceasedSons.count"
                                            @change="updateNames(formData.heirs.children.deceasedSons)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                            <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, 'deceasedSons') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-if="formData.heirs.children.deceasedSons.count > 0"
                                        class="space-y-4 ml-4 pl-4 border-l-2 border-green-200">
                                        <!--
                                                                                            v-for: For each deceased son, input his name and collect information about his children (grandsons and granddaughters).
                                                                                            This loop ensures that each deceased son's descendants are tracked for inheritance calculations.
                                                                                        -->
                                        <div v-for="(son, index) in formData.heirs.children.deceasedSons.names"
                                            :key="index" class="space-y-4">
                                            <div class="space-y-2">
                                                <input type="text" v-model="son.name"
                                                    :placeholder="`মৃত ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                            </div>
                                            <!-- Input for descendants of deceased son -->
                                            <div class="bg-[#F5FFE8] p-4 rounded-lg space-y-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">ছেলের
                                                            সংখ্যা:</label>
                                                        <select v-model="son.sonsCount" @change="updateSonsNames(son)"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'sons') }}</option>
                                                        </select>
                                                        <!--
                                                                                                            v-for: For each deceased son's son (grandson), input his name.
                                                                                                            Grandsons through deceased sons can inherit if their father (the deceased's son) is not alive at the time of the deceased's death.
                                                                                                        -->
                                                        <div v-if="son.sonsCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(grandson, gIndex) in son.sonsNames"
                                                                :key="gIndex">
                                                                <input type="text" v-model="grandson.name"
                                                                    :placeholder="`${son.name || 'মৃত ছেলে'}-এর ছেলে ${getBengaliOrdinal(gIndex+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">মেয়ের
                                                            সংখ্যা:</label>
                                                        <select v-model="son.daughtersCount"
                                                            @change="updateDaughtersNames(son)"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'daughters') }}</option>
                                                        </select>
                                                        <!--
                                                                                                            v-for: For each deceased son's daughter (granddaughter), input her name.
                                                                                                            Granddaughters through deceased sons may also inherit under certain circumstances, especially if there are no surviving sons or grandsons.
                                                                                                        -->
                                                        <div v-if="son.daughtersCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(granddaughter, dIndex) in son.daughtersNames"
                                                                :key="dIndex">
                                                                <input type="text" v-model="granddaughter.name"
                                                                    :placeholder="`${son.name || 'মৃত ছেলে'}-এর মেয়ে ${getBengaliOrdinal(dIndex+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--
                                                                                Deceased Daughters Section:
                                                                                This section collects the number of deceased daughters, their names, and their children (grandchildren through daughters).

                                                                                Islamic inheritance law may allow the children of deceased daughters (especially grandsons) to inherit if certain conditions are met, such as the absence of direct male descendants. For each deceased daughter, input:
                                                                                - The daughter's name
                                                                                - The number and names of her sons (grandsons)
                                                                                - The number and names of her daughters (granddaughters)
                                                                                This information is essential for correct inheritance distribution.
                                                                            -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="mb-4 pb-2 border-b border-gray-200">
                                    <h3 class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName('মৃত ব্যক্তির মৃত মেয়ে') }}</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-4">
                                        <label class="text-sm font-medium text-black">মৃত মেয়ের সংখ্যা:</label>
                                        <select v-model="formData.heirs.children.deceasedDaughters.count"
                                            @change="updateNames(formData.heirs.children.deceasedDaughters)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                            <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, 'deceasedDaughters') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-if="formData.heirs.children.deceasedDaughters.count > 0"
                                        class="space-y-4 ml-4 pl-4 border-l-2 border-green-200">
                                        <div v-for="(daughter, index) in formData.heirs.children.deceasedDaughters.names"
                                            :key="index" class="space-y-4">
                                            <div class="space-y-2">
                                                <input type="text" v-model="daughter.name"
                                                    :placeholder="`মৃত মেয়ে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                            </div>
                                            <div class="bg-[#F5FFE8] p-4 rounded-lg space-y-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">ছেলের
                                                            সংখ্যা:</label>
                                                        <select v-model="daughter.sonsCount"
                                                            @change="updateSonsNames(daughter)"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'sons') }}</option>
                                                        </select>
                                                        <div v-if="daughter.sonsCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(grandson, gIndex) in daughter.sonsNames"
                                                                :key="gIndex">
                                                                <input type="text" v-model="grandson.name"
                                                                    :placeholder="`${daughter.name || 'মৃত মেয়ে'}-এর ছেলে ${getBengaliOrdinal(gIndex+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">মেয়ের
                                                            সংখ্যা:</label>
                                                        <select v-model="daughter.daughtersCount"
                                                            @change="updateDaughtersNames(daughter)"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'daughters') }}</option>
                                                        </select>
                                                        <div v-if="daughter.daughtersCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(granddaughter, dIndex) in daughter.daughtersNames"
                                                                :key="dIndex">
                                                                <input type="text" v-model="granddaughter.name"
                                                                    :placeholder="`${daughter.name || 'মৃত মেয়ে'}-এর মেয়ে ${getBengaliOrdinal(dIndex+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                                                                            Siblings Section:
                                                                            This section gathers information about the deceased's siblings (brothers and sisters) and their descendants. In Islamic inheritance law, siblings can inherit if there are no direct male descendants (sons/grandsons). The code tracks:
                                                                            - Number and names of brothers and sisters.
                                                                            - For brothers: whether they have sons (nephews) or grandsons (great-nephews), who may inherit if the brother is deceased and the deceased has no direct male descendants.

                                                                            The v-for loops allow dynamic input for each sibling and their descendants. Conditional blocks (v-if/v-else) handle special inheritance scenarios, such as when brothers have no sons, and check for the existence of grandsons.

                                                                            This structure ensures all eligible heirs among siblings and their descendants are captured for correct share calculation.
                                                                        -->
                        <div class="bg-white p-4 rounded-lg border border-gray-300 shadow-sm">
                            <div class="space-y-4">
                                <div v-for="(sibling, key) in formData.heirs.siblings" :key="key"
                                    class="space-y-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                        <label
                                            class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(sibling.label) }}:</label>
                                        <select v-model="sibling.count" @change="updateNames(sibling)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                            <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, key) }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <div v-for="(member, index) in sibling.names" :key="index">
                                            <input type="text" v-model="member.name"
                                                :placeholder="`${replaceDeceasedName(sibling.label)} ${getBengaliOrdinal(index + 1)} এর নাম`"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                        </div>
                                    </div>
                                    <div v-if="key === 'brothers' && sibling.count === 0"
                                        class="ml-4 pl-4 border-l-2 border-green-200 space-y-4">
                                        <div class="space-y-4">
                                            <div class="flex flex-col items-start gap-3">
                                                <label
                                                    class="text-sm font-medium text-black flex-1">@{{ replaceDeceasedName('মৃত ব্যক্তির সহোদর ভাই এর কোন ছেলে আছে?') }}</label>
                                                <div class="flex gap-4">
                                                    <label class="flex items-center space-x-2">
                                                        <input type="radio" value="yes" v-model="sibling.hasSons"
                                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                        <span class="text-black">হ্যাঁ</span>
                                                    </label>
                                                    <label class="flex items-center space-x-2">
                                                        <input type="radio" value="no" v-model="sibling.hasSons"
                                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                        <span class="text-black">না</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div v-if="sibling.hasSons === 'yes'" class="space-y-4">
                                                <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                                    <label
                                                        class="text-sm font-medium text-black">@{{ replaceDeceasedName('ছেলের সংখ্যা') }}:</label>
                                                    <select v-model="sibling.sonsCount" @change="updateSonsNames(sibling)"
                                                        class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                        <option v-for="n in 21" :value="n - 1">
                                                            @{{ getBanglaNumberLabel(n - 1, 'sons') }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="sibling.sonsCount > 0" class="space-y-2 ml-2">
                                                    <div v-for="(son, index) in sibling.sonsNames" :key="index">
                                                        <input type="text" v-model="son.name"
                                                            :placeholder="`সহোদর ভাই এর ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                    </div>
                                                </div>
                                                <div v-if="sibling.sonsCount === 0"
                                                    class="ml-4 pl-4 border-l-2 border-green-200 space-y-4">
                                                    <div class="flex flex-col items-start gap-3">
                                                        <label
                                                            class="text-sm font-medium text-black flex-1">@{{ replaceDeceasedName('মৃত ব্যক্তির সহোদর ভাই এর ছেলের ছেলে আছে?') }}</label>
                                                        <div class="flex gap-4">
                                                            <label class="flex items-center space-x-2">
                                                                <input type="radio" value="yes"
                                                                    v-model="sibling.hasGrandsons"
                                                                    class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                                <span class="text-black">হ্যাঁ</span>
                                                            </label>
                                                            <label class="flex items-center space-x-2">
                                                                <input type="radio" value="no"
                                                                    v-model="sibling.hasGrandsons"
                                                                    class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                                <span class="text-black">না</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div v-if="sibling.hasGrandsons === 'yes'" class="space-y-4">
                                                        <div
                                                            class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                                            <label
                                                                class="text-sm font-medium text-black">@{{ replaceDeceasedName('ছেলের ছেলের সংখ্যা') }}:</label>
                                                            <select v-model="sibling.grandsonsCount"
                                                                @change="updateGrandsonsNames(sibling)"
                                                                class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                                <option v-for="n in 21" :value="n - 1">
                                                                    @{{ getBanglaNumberLabel(n - 1, 'grandsons') }}</option>
                                                            </select>
                                                        </div>
                                                        <div v-if="sibling.grandsonsCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(grandson, index) in sibling.grandsonsNames"
                                                                :key="index">
                                                                <input type="text" v-model="grandson.name"
                                                                    :placeholder="`সহোদর ভাই এর ছেলের ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--
                                                                        Grandparents Section:
                                                                        This section collects the status (alive/dead) of the deceased's grandparents. According to Islamic inheritance law, grandparents may inherit only if the relevant parent (father or mother) is not alive at the time of the deceased's death. For example:
                                                                        - The paternal grandfather/grandmother can only inherit if the father is deceased.
                                                                        - The maternal grandmother can only inherit if the mother is deceased.

                                                                        The :disabled bindings on the radio buttons enforce this rule in the UI, preventing users from selecting a grandparent as alive if the corresponding parent is marked alive.

                                                                        The v-for loop iterates over all grandparent relations, displaying input options for each. This ensures that only eligible grandparents are considered for inheritance calculations.
                                                                    -->
                    <div class="grid grid-cols-1 md:grid-cols-2 border-t gap-3 md:gap-4 pt-4">
                        <div v-for="(relation, key) in formData.heirs.aliveGrandParentStatus" :key="key"
                            class="flex flex-col gap-2 mb-2">
                            <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
                                <label
                                    class="w-full md:w-1/3 text-sm font-semibold text-[#006F45]">@{{ grandparentLabels[key] }}:</label>
                                <div class="flex gap-3">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="alive" v-model="relation.status"
                                            :disabled="(key === 'maternalGrandMother' && formData.heirs.aliveParentStatus.mother
                                                .status === 'alive') || (['paternalGrandFather',
                                                    'paternalGrandMother'
                                                ].includes(key) && formData.heirs.aliveParentStatus.father
                                                .status === 'alive')"
                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] disabled:opacity-50">
                                        <span class="text-black">জীবিত</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="dead" v-model="relation.status"
                                            :disabled="(key === 'maternalGrandMother' && formData.heirs.aliveParentStatus.mother
                                                .status === 'alive') || (['paternalGrandFather',
                                                    'paternalGrandMother'
                                                ].includes(key) && formData.heirs.aliveParentStatus.father
                                                .status === 'alive')"
                                            class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] disabled:opacity-50">
                                        <span class="text-black">মৃত</span>
                                    </label>
                                </div>
                            </div>
                            <div v-if="relation.status === 'alive'" class="w-full">
                                <input type="text" v-model="relation.name"
                                    :placeholder="`${grandparentLabels[key]}-এর নাম`"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 transition-all duration-200">
                            </div>
                        </div>
                    </div>
                    <!--
                                                                        Other Relatives Section:
                                                                        This section handles additional possible heirs, such as uncles, aunts, or other extended family members. Islamic inheritance law includes these relatives only if there are no closer heirs (like children, siblings, or parents). For each relative, the code:
                                                                        - Collects the number and names of each type (e.g., paternal uncle, maternal aunt).
                                                                        - Tracks whether the relative has sons (e.g., cousins) or grandsons, who may inherit if their parent is not alive and there are no closer heirs.

                                                                        The v-for loops allow dynamic input for each relative and their descendants. Conditional v-if/v-else blocks manage special cases, such as asking about sons or grandsons if the primary relative is absent. The :disabled binding ensures only eligible relatives are selectable based on other heir data.

                                                                        This structure ensures the inheritance calculation is comprehensive and compliant with Islamic rules, capturing all possible heirs in complex family scenarios.
                                                                    -->
                    <div class="bg-white p-4 rounded-lg border border-gray-300 shadow-sm">
                        <div class="space-y-6">
                            <div v-for="(relative, key) in formData.heirs.otherRelatives" :key="key"
                                class="space-y-4 pb-4 border-b border-gray-200 last:border-0">
                                <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                    <label class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(relative.label) }}:</label>
                                    <select v-model="relative.count" @change="updateNames(relative)"
                                        :disabled="isRelativeDisabled(key)"
                                        class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 transition-all duration-200">
                                        <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, key) }}</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <div v-for="(member, index) in relative.names" :key="index">
                                        <input type="text" v-model="member.name" :disabled="isRelativeDisabled(key)"
                                            :placeholder="`${replaceDeceasedName(relative.label)} ${getBengaliOrdinal(index + 1)} এর নাম`"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 transition-all duration-200">
                                    </div>
                                </div>
                                <template v-if="relative.count === 0">
                                    <div v-for="config in relativeConfigs" :key="config.key">
                                        <template v-if="key === config.key">
                                            <div class="ml-4 pl-4 border-l-2 border-green-200 space-y-4">
                                                <div class="flex flex-col items-start gap-3">
                                                    <label
                                                        class="text-sm font-medium text-black flex-1">@{{ replaceDeceasedName(config.sonsQuestion) }}</label>
                                                    <div class="flex gap-4">
                                                        <label class="flex items-center space-x-2">
                                                            <input type="radio" value="yes"
                                                                v-model="relative.hasSons"
                                                                :disabled="isRelativeDisabled(key)"
                                                                class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                            <span class="text-black">হ্যাঁ</span>
                                                        </label>
                                                        <label class="flex items-center space-x-2">
                                                            <input type="radio" value="no"
                                                                v-model="relative.hasSons"
                                                                :disabled="isRelativeDisabled(key)"
                                                                class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                            <span class="text-black">না</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div v-if="relative.hasSons === 'yes'" class="space-y-4">
                                                    <div
                                                        class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                                        <label
                                                            class="text-sm font-medium text-black">@{{ replaceDeceasedName(config.sonsLabel) }}:</label>
                                                        <select v-model="relative.sonsCount"
                                                            @change="updateSonsNames(relative)"
                                                            :disabled="isRelativeDisabled(key)"
                                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 transition-all duration-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'sons') }}</option>
                                                        </select>
                                                    </div>
                                                    <div v-if="relative.sonsCount > 0" class="space-y-2 ml-2">
                                                        <div v-for="(son, index) in relative.sonsNames"
                                                            :key="index">
                                                            <input type="text" v-model="son.name"
                                                                :disabled="isRelativeDisabled(key)"
                                                                :placeholder="`${config.placeholderPrefix} ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 transition-all duration-200">
                                                        </div>
                                                    </div>
                                                    <div v-if="relative.sonsCount === 0"
                                                        class="ml-4 pl-4 border-l-2 border-green-200 space-y-4">
                                                        <div class="flex flex-col items-start gap-3">
                                                            <label
                                                                class="text-sm font-medium text-black flex-1">@{{ replaceDeceasedName(config.grandsonsQuestion) }}</label>
                                                            <div class="flex gap-4">
                                                                <label class="flex items-center space-x-2">
                                                                    <input type="radio" value="yes"
                                                                        v-model="relative.hasGrandsons"
                                                                        :disabled="isRelativeDisabled(key)"
                                                                        class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                                    <span class="text-black">হ্যাঁ</span>
                                                                </label>
                                                                <label class="flex items-center space-x-2">
                                                                    <input type="radio" value="no"
                                                                        v-model="relative.hasGrandsons"
                                                                        :disabled="isRelativeDisabled(key)"
                                                                        class="h-4 w-4 text-[#006F45] accent-[#006F45] border border-gray-300 focus:ring-[#006F45] transition-all duration-200">
                                                                    <span class="text-black">না</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div v-if="relative.hasGrandsons === 'yes'" class="space-y-4">
                                                            <div
                                                                class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                                                <label
                                                                    class="text-sm font-medium text-black">@{{ replaceDeceasedName(config.grandsonsLabel) }}:</label>
                                                                <select v-model="relative.grandsonsCount"
                                                                    @change="updateGrandsonsNames(relative)"
                                                                    :disabled="isRelativeDisabled(key)"
                                                                    class="w-full md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 transition-all duration-200">
                                                                    <option v-for="n in 21" :value="n - 1">
                                                                        @{{ getBanglaNumberLabel(n - 1, 'grandsons') }}</option>
                                                                </select>
                                                            </div>
                                                            <div v-if="relative.grandsonsCount > 0"
                                                                class="space-y-2 ml-2">
                                                                <div v-for="(grandson, index) in relative.grandsonsNames"
                                                                    :key="index">
                                                                    <input type="text" v-model="grandson.name"
                                                                        :disabled="isRelativeDisabled(key)"
                                                                        :placeholder="`${config.placeholderPrefix} ছেলের ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 transition-all duration-200">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Tab 3: Summary of All Data -->
            <template v-if="activeTab === 3">
                <!-- Summary Tab: Shows all entered and calculated data for final review -->
                <!-- Summary Tab Main Container -->
                <div class="space-y-6">
                    <!-- Header Section -->
                    <div class="text-center bg-gradient-to-r from-[#006F45] to-[#03442C] text-white p-6 rounded-lg">
                        <h3 class="text-2xl font-bold mb-2">এক নজরে সমস্ত তথ্য</h3>
                        <p class="text-green-100">নিচের তথ্যগুলো পর্যালোচনা করে বন্টন গণনা করুন</p>
                    </div>
                    <!-- Deceased Person Info Summary -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-[#006F45] text-white px-6 py-3">
                            <h4 class="text-lg font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                মৃত ব্যক্তির তথ্য
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 mb-1">নাম</span>
                                    <span class="font-medium text-gray-900">@{{ formData.deceasedInfo.name || 'নাম দেওয়া হয়নি' }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 mb-1">লিঙ্গ</span>
                                    <span class="font-medium text-gray-900">@{{ formData.deceasedInfo.gender === 'male' ? 'পুরুষ' : 'নারী' }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 mb-1">মৃত্যুর তারিখ</span>
                                    <span class="font-medium text-gray-900">@{{ formatDate(formData.deceasedInfo.deathDate) || 'তারিখ দেওয়া হয়নি' }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 mb-1">মৃত্যুর সময়</span>
                                    <span class="font-medium text-gray-900">@{{ formatTime(formData.deceasedInfo.deathTime) || 'সময় দেওয়া হয়নি' }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 mb-1">বৈবাহিক অবস্থা</span>
                                    <span class="font-medium text-gray-900">@{{ formData.deceasedInfo.maritalStatus === 'married' ? 'বিবাহিত' : (formData.deceasedInfo.maritalStatus === 'unmarried' ? 'অবিবাহিত' : 'তালাকপ্রাপ্ত') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Asset Summary -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-[#006F45] text-white px-6 py-3">
                            <h4 class="text-lg font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                সম্পত্তির বিবরণ
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="(asset, key) in formData.assets" :key="key"
                                    class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900">@{{ asset.label }}</span>
                                        <p class="text-sm text-gray-600">@{{ asset.placeholder }}</p>
                                    </div>
                                    <span class="font-bold text-[#006F45] text-lg">@{{ asset.value || '০' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Heirs Info Summary: Shows parents, siblings, and other relatives using v-for and conditionals -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-[#006F45] text-white px-6 py-3">
                            <h4 class="text-lg font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                ওয়ারিশদের তথ্য
                            </h4>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <h5 class="text-md font-semibold text-[#006F45] mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    পিতা-মাতা
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="(parent, key) in formData.heirs.aliveParentStatus" :key="key"
                                        class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="font-medium">@{{ parentLabels[key] }}</span>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium"
                                            :class="parent.status === 'alive' ? 'bg-green-100 text-green-800' :
                                                'bg-red-100 text-red-800'">
                                            @{{ parent.status === 'alive' ? (parent.name || 'নাম দেওয়া হয়নি') : 'মৃত' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Spouse -->
                            <div>
                                <h5 class="text-md font-semibold text-[#006F45] mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    সহধর্মিণী/স্বামী
                                </h5>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div v-if="formData.deceasedInfo.gender === 'male'">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-medium">স্ত্রীর সংখ্যা</span>
                                            <span
                                                class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                @{{ formData.heirs.spouseWives.count }} জন
                                            </span>
                                        </div>
                                        <div v-if="formData.heirs.spouseWives.count > 0" class="space-y-1">
                                            <div v-for="(wife, index) in formData.heirs.spouseWives.names"
                                                :key="index" class="text-sm text-gray-700">
                                                @{{ index + 1 }}. @{{ wife.name || `${getBengaliOrdinal(index + 1)} স্ত্রীর নাম` }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium">স্বামীর অবস্থা</span>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                                :class="formData.heirs.spouseStatus === 'alive' ?
                                                    'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                @{{ formData.heirs.spouseStatus === 'alive' ? (formData.heirs.spouseName || 'স্বামী') : 'মৃত' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-semibold text-[#006F45] mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    সন্তান
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="childType in ['aliveSons', 'aliveDaughters', 'deceasedSons', 'deceasedDaughters']"
                                        :key="childType">
                                        <div v-if="formData.heirs.children[childType].count > 0"
                                            class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-medium">@{{ replaceDeceasedName(formData.heirs.children[childType].label) }}</span>
                                                <span
                                                    class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    @{{ formData.heirs.children[childType].count }} জন
                                                </span>
                                            </div>
                                            <div v-for="(child, index) in formData.heirs.children[childType].names"
                                                :key="index" class="ml-4 border-l-2 border-green-200 pl-2 mb-2">
                                                <p class="text-sm">@{{ child.name || `মৃত সন্তান ${getBengaliOrdinal(index+1)}` }}</p>
                                                <div v-if="childType.includes('deceased') && child.sonsNames.length > 0"
                                                    class="ml-3 mt-1">
                                                    <p class="text-xs font-medium text-gray-600">ছেলে
                                                        (@{{ child.sonsNames.length }} জন):</p>
                                                    <div v-for="(son, sIndex) in child.sonsNames" :key="sIndex">
                                                        <p class="text-xs text-gray-600">@{{ sIndex + 1 }}.
                                                            @{{ son.name || 'নামহীন' }}</p>
                                                    </div>
                                                </div>
                                                <div v-if="childType.includes('deceased') && child.daughtersNames.length > 0"
                                                    class="ml-3 mt-1">
                                                    <p class="text-xs font-medium text-gray-600">মেয়ে
                                                        (@{{ child.daughtersNames.length }} জন):</p>
                                                    <div v-for="(daughter, dIndex) in child.daughtersNames"
                                                        :key="dIndex">
                                                        <p class="text-xs text-gray-600">@{{ dIndex + 1 }}.
                                                            @{{ daughter.name || 'নামহীন' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-semibold text-[#006F45] mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    ভাইবোন
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="siblingType in ['brothers', 'sisters']" :key="siblingType">
                                        <div v-if="formData.heirs.siblings[siblingType].count > 0"
                                            class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-medium">@{{ replaceDeceasedName(formData.heirs.siblings[siblingType].label) }}</span>
                                                <span
                                                    class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    @{{ formData.heirs.siblings[siblingType].count }} জন
                                                </span>
                                            </div>
                                            <div v-for="(sibling, index) in formData.heirs.siblings[siblingType].names"
                                                :key="index" class="ml-4 border-l-2 border-green-200 pl-3 mb-2">
                                                <div class="text-sm">@{{ sibling.name || `${getBengaliOrdinal(index + 1)} এর নাম` }}</div>
                                                <div v-if="siblingType === 'brothers' && sibling.sonsNames.length > 0"
                                                    class="bg-[#F5FFE8] p-2 rounded-lg mt-1">
                                                    <p class="text-xs font-medium mb-1">@{{ replaceDeceasedName('ছেলে') }}
                                                        (@{{ sibling.sonsNames.length }} জন):</p>
                                                    <div v-for="(son, sIndex) in sibling.sonsNames" :key="sIndex"
                                                        class="ml-3 text-xs text-gray-600">
                                                        @{{ son.name || `${getBengaliOrdinal(sIndex + 1)} ছেলে` }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else-if="siblingType === 'brothers'" class="p-3 bg-gray-50 rounded-lg">
                                            <div v-if="formData.heirs.siblings.brothers.hasSons === 'yes'">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="font-medium">@{{ replaceDeceasedName('মৃত সহোদর ভাই এর ছেলেরা') }}</span>
                                                    <span
                                                        class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                                        মৃত ভাইয়ের ছেলে
                                                    </span>
                                                </div>
                                                <div v-for="(son, index) in formData.heirs.siblings.brothers.sonsNames"
                                                    :key="index"
                                                    class="ml-4 border-l-2 border-green-200 pl-3 mb-1">
                                                    <span class="text-sm">@{{ son.name || `${getBengaliOrdinal(index + 1)} ছেলে` }}</span>
                                                </div>
                                                <div
                                                    v-if="formData.heirs.siblings.brothers.sonsCount === 0 && formData.heirs.siblings.brothers.hasGrandsons === 'yes'">
                                                    <div class="flex justify-between items-center mb-2 mt-2">
                                                        <span class="font-medium text-sm">@{{ replaceDeceasedName('মৃত সহোদর ভাই এর ছেলের ছেলেরা') }}</span>
                                                        <span
                                                            class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            নাতি
                                                        </span>
                                                    </div>
                                                    <div v-for="(grandson, index) in formData.heirs.siblings.brothers.grandsonsNames"
                                                        :key="index"
                                                        class="ml-4 border-l-2 border-green-200 pl-3 mb-1">
                                                        <span class="text-sm">@{{ grandson.name || `${getBengaliOrdinal(index + 1)} ছেলের ছেলে` }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="text-gray-600 text-sm">@{{ replaceDeceasedName('কোন সহোদর ভাই নেই') }}</div>
                                        </div>
                                        <div v-else-if="siblingType === 'sisters' && formData.heirs.siblings.sisters.count === 0"
                                            class="p-3 bg-gray-50 rounded-lg">
                                            <span class="text-gray-600 text-sm">@{{ replaceDeceasedName('কোন সহোদর বোন নেই') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-semibold text-[#006F45] mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    অন্যান্য আত্মীয়
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="(relative, key) in formData.heirs.otherRelatives" :key="key">
                                        <div v-if="(relative.count > 0 || relative.hasSons === 'yes') && !isRelativeDisabled(key)"
                                            class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-medium">@{{ replaceDeceasedName(relative.label) }}</span>
                                                <span class="px-3 py-1 rounded-full text-sm font-medium"
                                                    :class="relative.count > 0 ? 'bg-blue-100 text-blue-800' :
                                                        'bg-red-100 text-red-800'">
                                                    <span v-if="relative.count > 0">@{{ relative.count }} জন</span>
                                                    <span v-else>মৃত</span>
                                                </span>
                                            </div>
                                            <div v-if="relative.count > 0" class="space-y-1">
                                                <div v-for="(member, index) in relative.names" :key="index"
                                                    class="text-sm">
                                                    @{{ member.name || `${getBengaliOrdinal(index + 1)} এর নাম` }}
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div v-if="relative.hasSons === 'yes'"
                                                    class="ml-4 border-l-2 border-green-200 pl-3 mt-2">
                                                    <p class="font-medium text-sm mb-1">ছেলেরা:</p>
                                                    <div v-for="(son, index) in relative.sonsNames" :key="index"
                                                        class="text-sm text-gray-600 mb-1">
                                                        @{{ son.name || `${getBengaliOrdinal(index + 1)} ছেলে` }}
                                                    </div>
                                                    <div v-if="relative.sonsCount === 0 && relative.hasGrandsons === 'yes'"
                                                        class="ml-4 border-l-2 border-green-200 pl-3 mt-2">
                                                        <p class="font-medium text-sm mb-1">ছেলের ছেলেরা:</p>
                                                        <div v-for="(grandson, index) in relative.grandsonsNames"
                                                            :key="index" class="text-sm text-gray-600 mb-1">
                                                            @{{ grandson.name || `${getBengaliOrdinal(index + 1)} ছেলের ছেলে` }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-semibold text-[#006F45] mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    দাদা-দাদি-নানি
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div v-for="(grandparent, key) in formData.heirs.aliveGrandParentStatus"
                                        :key="key"
                                        class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="font-medium">@{{ grandparentLabels[key] }}</span>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium"
                                            :class="grandparent.status === 'alive' ? 'bg-green-100 text-green-800' :
                                                'bg-red-100 text-red-800'">
                                            @{{ grandparent.status === 'alive' ? (grandparent.name || grandparentLabels[key]) : 'মৃত' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Action Button -->
                    <div class="text-center">
                        <button @click="submitForm"
                            class="px-12 py-4 bg-gradient-to-r from-[#006F45] to-[#03442C] text-white rounded-lg font-semibold text-lg hover:from-[#03442C] hover:to-[#006F45] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-200 focus:ring-offset-2 shadow-lg transform hover:scale-105">
                            <svg class="w-6 h-6 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            বন্টন গণনা করুন
                        </button>
                    </div>
            </template>
        </div>
        <!-- Navigation Buttons -->
        <div class="flex justify-between gap-2 mt-6">
            <button @click="prevStep" class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors"
                :class="activeTab === 0 ?
                    'bg-gray-200 text-gray-600 border-gray-300 cursor-not-allowed' :
                    'bg-[#006F45] text-white hover:bg-[#03442C] border-[#006F45]'">
                &lt; পূর্ববর্তী
            </button>
            <button @click="nextStep" class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors"
                :class="activeTab === buttons.length - 1 ?
                    'bg-gray-200 text-gray-600 border-gray-300 cursor-not-allowed' :
                    'bg-[#006F45] text-white hover:bg-[#03442C] border-[#006F45]'">
                পরবর্তী &gt;
            </button>
        </div>
    </div>

    <style>
        input,
        select,
        textarea {
            @apply text-sm md:text-base;
            min-height: 2.5rem;
            font-family: 'Noto Sans Bengali', 'SolaimanLipi', 'Kalpurush', 'AdorshoLipi', Arial, sans-serif;
            line-height: 1.6;
            text-rendering: optimizeLegibility;
            -webkit-font-feature-settings: "kern" 1;
            font-feature-settings: "kern" 1;
        }

        input[type="radio"]:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
            background-color: #006F45;
            border-color: #006F45;
        }

        input,
        select,
        textarea {
            @apply transition-all duration-200 ease-in-out;
        }

        input:focus,
        select:focus,
        textarea:focus {
            @apply ring-2 ring-green-200 border-[#006F45];
        }

        /* Bengali text rendering fixes */
        select option {
            font-family: 'Noto Sans Bengali', 'SolaimanLipi', 'Kalpurush', 'AdorshoLipi', Arial, sans-serif;
            padding: 8px 12px;
            line-height: 1.6;
            text-rendering: optimizeLegibility;
        }

        /* Ensure proper spacing for Bengali characters */
        select {
            padding: 8px 12px;
            min-width: 200px;
        }

        /* Fix for Bengali vowel signs and complex characters */
        * {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Specific fix for marital status dropdown */
        select[name="maritalStatus"],
        select[data-marital-status] {
            min-width: 250px;
            white-space: nowrap;
            overflow: visible;
        }

        /* Ensure Bengali text doesn't get cut off */
        .bengali-text {
            font-family: 'Noto Sans Bengali', 'SolaimanLipi', 'Kalpurush', 'AdorshoLipi', Arial, sans-serif;
            line-height: 1.8;
            letter-spacing: 0.5px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Constants
        const CONSTANTS = {
            BENGALI_DIGITS: ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'],
            ENGLISH_DIGITS: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            DEFAULT_DECEASED_NAME: 'মৃত ব্যক্তির',
            SAVE_DEBOUNCE_DELAY: 500,
            MAX_FAMILY_MEMBERS: 20,
            STORAGE_KEY: 'calculator_data'
        };

        // Utility functions
        const Utils = {
            // Convert English numbers to Bengali numerals
            toBengaliNumerals(value) {
                return value.replace(/\d/g, (digit) => {
                    return CONSTANTS.BENGALI_DIGITS[parseInt(digit)];
                });
            },

            // Convert Bengali numerals to English numbers
            toEnglishNumerals(value) {
                return value.replace(/[০-৯]/g, (digit) => {
                    return CONSTANTS.ENGLISH_DIGITS[CONSTANTS.BENGALI_DIGITS.indexOf(digit)];
                });
            },

            // Get current date in YYYY-MM-DD format
            getCurrentDate() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            // Get current time in HH:MM format
            getCurrentTime() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            },

            // Safe JSON parse with fallback
            safeJsonParse(str, fallback = {}) {
                try {
                    return JSON.parse(str) || fallback;
                } catch (e) {
                    console.warn('JSON parse error:', e);
                    return fallback;
                }
            },

            // Safe sessionStorage operations
            safeStorage: {
                getItem(key) {
                    try {
                        return sessionStorage.getItem(key);
                    } catch (e) {
                        console.warn('Storage get error:', e);
                        return null;
                    }
                },
                setItem(key, value) {
                    try {
                        sessionStorage.setItem(key, value);
                        return true;
                    } catch (e) {
                        console.warn('Storage set error:', e);
                        return false;
                    }
                }
            }
        };

        // Data factory functions
        const DataFactory = {
            // Create asset object
            createAsset(label, placeholder, hint = '') {
                return {
                    label,
                    value: '০',
                    placeholder,
                    hint,
                    showHint: false,
                    numericValue: 0
                };
            },

            // Create family member object
            createFamilyMember(label, status = 'alive') {
                return {
                    label,
                    status,
                    name: ''
                };
            },

            // Create child category object
            createChildCategory(label) {
                return {
                    label,
                    count: 0,
                    names: []
                };
            },

            // Create deceased child entry
            createDeceasedChildEntry() {
                return {
                    name: '',
                    sonsCount: 0,
                    sonsNames: [],
                    daughtersCount: 0,
                    daughtersNames: []
                };
            },

            // Create sibling category
            createSiblingCategory(label) {
                return {
                    label,
                    count: 0,
                    names: [],
                    hasSons: 'no',
                    sonsCount: 0,
                    sonsNames: [],
                    hasGrandsons: 'no',
                    grandsonsCount: 0,
                    grandsonsNames: []
                };
            },

            // Create relative object
            createRelative(label) {
                return {
                    label,
                    count: 0,
                    names: []
                };
            },

            // Create relative with children
            createRelativeWithChildren(label) {
                return {
                    ...this.createRelative(label),
                    hasSons: 'no',
                    sonsCount: 0,
                    sonsNames: [],
                    hasGrandsons: 'no',
                    grandsonsCount: 0,
                    grandsonsNames: []
                };
            }
        };

        Vue.createApp({
            // Lifecycle hooks
            mounted() {
                this.initializeApp();
            },

            beforeUnmount() {
                this.cleanup();
            },

            // Data initialization
            data() {
                return {
                    // UI state
                    activeTab: 0,
                    saveTimeout: null,

                    // Navigation buttons
                    buttons: [{
                            label: "মৃত ব্যক্তির তথ্য"
                        },
                        {
                            label: "মৃত ব্যক্তির সম্পত্তি"
                        },
                        {
                            label: "মৃত ব্যক্তির ওয়ারিশ"
                        },
                        {
                            label: "এক নজরে"
                        }
                    ],

                    // Form data
                    formData: this.initializeFormData(),

                    // Relative configurations
                    relativeConfigs: this.createRelativeConfigs()
                };
            },
            // Computed properties
            computed: {
                deceasedName() {
                    return this.formData.deceasedInfo.name || CONSTANTS.DEFAULT_DECEASED_NAME;
                },

                grandparentLabels() {
                    return this.generateLabels(['দাদা', 'দাদি', 'নানি'], 'grandparent');
                },

                parentLabels() {
                    return this.generateLabels(['বাবা', 'মা'], 'parent');
                },

                isEditMode() {
                    const urlParams = new URLSearchParams(window.location.search);
                    return urlParams.get('edit') === '1';
                }
            },
            // Methods organized by functionality
            methods: {
                // ===== INITIALIZATION METHODS =====
                initializeApp() {
                    this.setupAxios();
                    this.checkUrlParameters();
                    this.loadStoredData();
                },

                setupAxios() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
                    }
                },

                checkUrlParameters() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const editMode = urlParams.get('edit');

                    // If edit=1, keep existing data (don't reset)
                    // If no edit parameter or edit=0, reset data
                    if (editMode !== '1') {
                        this.resetAllData();
                    }
                },

                resetAllData() {
                    // Clear sessionStorage
                    Utils.safeStorage.setItem(CONSTANTS.STORAGE_KEY, '');

                    // Reset form data to defaults
                    this.formData = this.getDefaultFormData();

                    // Clear any existing data from the element
                    const element = document.getElementById('calculator');
                    if (element) {
                        element.dataset.initial = '{}';
                    }
                },

                loadStoredData() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const editMode = urlParams.get('edit');

                    // Only load stored data if in edit mode
                    if (editMode === '1') {
                        if (!this.hasInitialData()) {
                            const storedData = Utils.safeStorage.getItem(CONSTANTS.STORAGE_KEY);
                            if (storedData) {
                                const parsedData = Utils.safeJsonParse(storedData);
                                if (this.isValidStoredData(parsedData)) {
                                    this.formData = this.mergeWithDefaults(parsedData);
                                }
                            }
                        }
                    }
                },

                hasInitialData() {
                    // Check if we have meaningful data (not just defaults)
                    return this.formData.deceasedInfo.name &&
                        this.formData.deceasedInfo.name.trim() !== '' &&
                        this.formData.assets.land.value &&
                        this.formData.assets.land.value !== '০';
                },

                isValidStoredData(data) {
                    return data && data.deceasedInfo && data.assets;
                },

                cleanup() {
                    if (this.saveTimeout) {
                        clearTimeout(this.saveTimeout);
                    }
                },

                // ===== DATA INITIALIZATION =====
                initializeFormData() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const editMode = urlParams.get('edit');

                    // If not in edit mode, return fresh defaults
                    if (editMode !== '1') {
                        return this.getDefaultFormData();
                    }

                    // If in edit mode, try to load existing data
                    const initialData = this.getInitialDataFromElement();
                    return this.mergeWithDefaults(initialData);
                },

                getInitialDataFromElement() {
                    const element = document.getElementById('calculator');
                    if (!element) return {};

                    return Utils.safeJsonParse(element.dataset.initial || '{}');
                },

                createRelativeConfigs() {
                    return [
                        this.createRelativeConfig('paternalHalfBrother',
                            'মৃত ব্যক্তির বৈমাতৃয় ভাই (মা ভিন্ন, বাবা এক) এর কোন ছেলে আছে?', 'বৈমাতৃয় ভাই'),
                        this.createRelativeConfig('paternalCousin', 'মৃত ব্যক্তির চাচাতো ভাই এর কোন ছেলে আছে?',
                            'চাচাতো ভাই'),
                        this.createRelativeConfig('paternalHalfCousin',
                            'মৃত ব্যক্তির বৈমাতৃয় (মা ভিন্ন, বাবা এক) চাচাতো ভাই এর কোন ছেলে আছে?',
                            'বৈমাতৃয় চাচাতো ভাই')
                    ];
                },

                // ===== VALIDATION METHODS =====
                validateFormData(data) {
                    return data && data.deceasedInfo && data.heirs;
                },

                // ===== DATA MANAGEMENT METHODS =====
                sanitizeFormData(data) {
                    this.sanitizeAssets(data.assets);
                    this.sanitizeChildren(data.heirs.children);
                    return data;
                },

                sanitizeAssets(assets) {
                    Object.values(assets).forEach(asset => {
                        asset.value = asset.numericValue !== undefined ? asset.numericValue : (Number(asset
                            .value) || 0);
                    });
                },

                sanitizeChildren(children) {
                    Object.values(children).forEach(child => {
                        child.count = Math.max(0, parseInt(child.count));
                    });
                },

                mergeAssets(defaultAssets, initialAssets) {
                    const merged = {
                        ...defaultAssets
                    };
                    if (initialAssets && typeof initialAssets === 'object') {
                        Object.keys(initialAssets).forEach(key => {
                            if (merged[key]) {
                                merged[key] = {
                                    ...merged[key],
                                    ...initialAssets[key]
                                };
                            }
                        });
                    }
                    return merged;
                },

                mergeHeirs(defaultHeirs, initialHeirs) {
                    const merged = {
                        ...defaultHeirs
                    };
                    if (initialHeirs && typeof initialHeirs === 'object') {
                        Object.keys(initialHeirs).forEach(key => {
                            if (merged[key]) {
                                if (typeof merged[key] === 'object' && !Array.isArray(merged[key])) {
                                    merged[key] = {
                                        ...merged[key],
                                        ...initialHeirs[key]
                                    };
                                } else {
                                    merged[key] = initialHeirs[key];
                                }
                            }
                        });
                    }
                    return merged;
                },

                mergeWithDefaults(initialData) {
                    const defaults = this.getDefaultFormData();
                    if (!this.isValidInitialData(initialData)) return defaults;

                    return this.sanitizeFormData({
                        deceasedInfo: {
                            ...defaults.deceasedInfo,
                            ...(initialData.deceasedInfo || {})
                        },
                        assets: this.mergeAssets(defaults.assets, initialData.assets),
                        heirs: this.mergeHeirs(defaults.heirs, initialData.heirs)
                    });
                },

                isValidInitialData(data) {
                    return data &&
                        Object.keys(data).length > 0 &&
                        typeof data === 'object' &&
                        !Array.isArray(data) &&
                        this.validateFormData(data);
                },

                getDefaultFormData() {
                    return {
                        deceasedInfo: this.createDeceasedInfo(),
                        assets: this.createAssets(),
                        heirs: this.createHeirs()
                    };
                },

                createDeceasedInfo() {
                    return {
                        name: '',
                        deathDate: Utils.getCurrentDate(),
                        gender: 'male',
                        deathTime: Utils.getCurrentTime(),
                        maritalStatus: 'married'
                    };
                },

                createAssets() {
                    const assetConfigs = [
                        ['জমির পরিমাণ', 'শতাংশ/কাঠা',
                            'মৃত ব্যক্তি মৃত্যুকালীন সময় রেখে যাওয়া জমির পরিমাণ। (কৃষি জমি, বাণিজ্যিক জমি বা প্লট—সব ধরনের জমি এর অন্তর্ভুক্ত)'
                        ],
                        ['ফ্ল্যাট', 'স্কয়ার ফিট',
                            'মৃত ব্যক্তির নামে যদি কোনো ফ্ল্যাট বা অ্যাপার্টমেন্ট থাকে, তাহলে সেটির বর্তমান বাজারমূল্য উল্লেখ করা যেতে পারে বা ফ্ল্যাটের আকার (বর্গফুট) উল্লেখ করতে পারেন।'
                        ],
                        ['নগদ টাকার পরিমাণ', 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত',
                            'মৃত ব্যক্তির কাছে থাকা নগদ টাকা এবং ব্যাংক অ্যাকাউন্টে জমা থাকা মোট অর্থের পরিমাণ এখানে লিখতে হবে। ব্যাংক অ্যাকাউন্টের মধ্যে সঞ্চয়ী হিসাব, চলতি হিসাব, ফিক্সড ডিপোজিট (FDR) এবং অন্যান্য যেকোনো ধরনের আমানত অন্তর্ভুক্ত হবে।'
                        ],
                        ['বিনিয়োগের পরিমাণ', 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত',
                            'মৃত ব্যক্তির নামে যদি কোনো শেয়ার, বন্ড, মিউচুয়াল ফান্ড, সরকারি সঞ্চয়পত্র বা অন্য কোনো বিনিয়োগ থাকে, তাহলে সেগুলোর বর্তমান বাজারমূল্য এখানে লিখতে হবে।'
                        ],
                        ['পাওনা টাকার পরিমাণ', 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত',
                            'যদি কোনো ব্যক্তি বা প্রতিষ্ঠানের কাছে মৃত ব্যক্তির পাওনা টাকা থাকে, তাহলে সেই টাকার পরিমাণ এখানে উল্লেখ করতে হবে। যেমন—যদি মৃত ব্যক্তি কাউকে ঋণ দিয়ে থাকেন অথবা কোনো কাজের বিনিময়ে মজুরি বা অন্য কোনো অর্থ পাওনা থাকে, তাহলে তা এখানে আসবে।'
                        ],
                        ['অপরিশোধিত ঋণ', 'টাকায়',
                            'মৃত ব্যক্তির নামে যদি কোনো অপরিশোধিত ঋণ থাকে, যেমনঃ ব্যাংক ঋণ, ক্রেডিট কার্ডের বকেয়া বা অন্য কোনো ব্যক্তিগত ঋণ, তাহলে সেই ঋণের পরিমাণ এখানে উল্লেখ করতে হবে। উত্তরাধিকারদের মধ্যে সম্পত্তি বণ্টনের আগে এই ঋণ পরিশোধ করা হবে।'
                        ],
                        ['অলংকারের পরিমাণ', 'টাকায়',
                            'মৃত ব্যক্তির কাছে থাকা স্বর্ণ, রুপা বা অন্য যেকোনো মূল্যবান অলংকার এখানে আসবে। এক্ষেত্রে অলংকারের পরিমাণ বা বর্তমান বাজারমূল্য হিসাব করে লিখতে পারেন।'
                        ]
                    ];

                    const assetKeys = ['land', 'flat', 'cash', 'investment', 'owedCash', 'unpaidDebt', 'jewellery'];
                    const assets = {};

                    assetConfigs.forEach((config, index) => {
                        assets[assetKeys[index]] = DataFactory.createAsset(config[0], config[1], config[2]);
                    });

                    return assets;
                },

                createAsset(label, placeholder, hint = '') {
                    return DataFactory.createAsset(label, placeholder, hint);
                },

                createHeirs() {
                    return {
                        spouseWives: {
                            count: 0,
                            names: []
                        },
                        spouseStatus: 'alive',
                        spouseName: '',
                        aliveParentStatus: {
                            father: DataFactory.createFamilyMember('মৃত ব্যক্তির বাবা'),
                            mother: DataFactory.createFamilyMember('মৃত ব্যক্তির মা')
                        },
                        aliveGrandParentStatus: {
                            paternalGrandFather: DataFactory.createFamilyMember('মৃত ব্যক্তির দাদা', 'dead'),
                            paternalGrandMother: DataFactory.createFamilyMember('মৃত ব্যক্তির দাদি', 'dead'),
                            maternalGrandMother: DataFactory.createFamilyMember('মৃত ব্যক্তির নানি', 'dead')
                        },
                        children: this.createChildren(),
                        siblings: this.createSiblings(),
                        otherRelatives: this.createOtherRelatives()
                    };
                },

                createFamilyMember(label, status = 'alive') {
                    return DataFactory.createFamilyMember(label, status);
                },

                createChildren() {
                    return {
                        aliveSons: DataFactory.createChildCategory('মৃত ব্যক্তির জীবিত ছেলে'),
                        aliveDaughters: DataFactory.createChildCategory('মৃত ব্যক্তির জীবিত মেয়ে'),
                        deceasedSons: this.createDeceasedChildCategory('মৃত ব্যক্তির মৃত ছেলে'),
                        deceasedDaughters: this.createDeceasedChildCategory('মৃত ব্যক্তির মৃত মেয়ে')
                    };
                },

                createChildCategory(label) {
                    return DataFactory.createChildCategory(label);
                },

                createDeceasedChildCategory(label) {
                    return {
                        label,
                        count: 0,
                        names: [DataFactory.createDeceasedChildEntry()]
                    };
                },

                createDeceasedChildEntry() {
                    return DataFactory.createDeceasedChildEntry();
                },

                createSiblings() {
                    return {
                        brothers: DataFactory.createSiblingCategory('মৃত ব্যক্তির সহোদর ভাই'),
                        sisters: DataFactory.createSiblingCategory('মৃত ব্যক্তির সহোদর বোন')
                    };
                },

                createSiblingCategory(label) {
                    return DataFactory.createSiblingCategory(label);
                },

                createOtherRelatives() {
                    const relativeConfigs = [
                        ['maternalHalfBrother', 'মৃত ব্যক্তির বৈপিত্রেয় ভাই', false],
                        ['maternalHalfSister', 'মৃত ব্যক্তির বৈপিত্রেয় বোন', false],
                        ['paternalHalfBrother', 'মৃত ব্যক্তির বৈমাতৃয় ভাই', true],
                        ['paternalHalfSister', 'মৃত ব্যক্তির বৈমাতৃয় বোন', false],
                        ['paternalUncle', 'মৃত ব্যক্তির চাচা', false],
                        ['paternalHalfUncle', 'মৃত ব্যক্তির বৈমাতৃয় চাচা', false],
                        ['paternalCousin', 'মৃত ব্যক্তির চাচাতো ভাই', true],
                        ['paternalHalfCousin', 'মৃত ব্যক্তির বৈমাতৃয় চাচাতো ভাই', true]
                    ];

                    const relatives = {};
                    relativeConfigs.forEach(([key, label, hasChildren]) => {
                        relatives[key] = hasChildren ?
                            DataFactory.createRelativeWithChildren(label) :
                            DataFactory.createRelative(label);
                    });

                    return relatives;
                },

                createRelative(label) {
                    return DataFactory.createRelative(label);
                },

                createRelativeWithChildren(label) {
                    return DataFactory.createRelativeWithChildren(label);
                },

                createRelativeConfig(key, question, prefix) {
                    return {
                        key,
                        borderColor: 'border-black',
                        grandsonBorderColor: 'border-black',
                        sonsQuestion: question,
                        sonsLabel: question.replace('আছে?', 'সংখ্যা'),
                        grandsonsQuestion: question.replace('ছেলে', 'ছেলের ছেলে'),
                        grandsonsLabel: question.replace('ছেলে', 'ছেলের ছেলে').replace('আছে?', 'সংখ্যা'),
                        placeholderPrefix: prefix
                    };
                },

                // ===== UTILITY METHODS =====
                generateLabels(labels, type) {
                    const name = this.deceasedName;
                    const result = {};

                    labels.forEach((label, index) => {
                        const key = type === 'parent' ?
                            (index === 0 ? 'father' : 'mother') :
                            (index === 0 ? 'paternalGrandFather' : index === 1 ? 'paternalGrandMother' :
                                'maternalGrandMother');

                        result[key] = name === CONSTANTS.DEFAULT_DECEASED_NAME ?
                            `${CONSTANTS.DEFAULT_DECEASED_NAME} ${label}` :
                            `${name}-এর ${label}`;
                    });

                    return result;
                },

                // ===== STORAGE METHODS =====
                debouncedSave() {
                    if (this.saveTimeout) {
                        clearTimeout(this.saveTimeout);
                    }
                    this.saveTimeout = setTimeout(() => {
                        Utils.safeStorage.setItem(CONSTANTS.STORAGE_KEY, JSON.stringify(this.formData));
                    }, CONSTANTS.SAVE_DEBOUNCE_DELAY);
                },

                // ===== FAMILY MEMBER UPDATE METHODS =====
                updateFamilyMembers(countKey, namesKey) {
                    return (relative) => {
                        const newCount = relative[countKey];
                        relative[namesKey] = Array.from({
                                length: newCount
                            }, (_, i) =>
                            relative[namesKey][i] || {
                                name: ''
                            }
                        );
                    };
                },

                updateWifeNames() {
                    this.updateFamilyMembers('count', 'names')(this.formData.heirs.spouseWives);
                },

                updateSonsNames(relative) {
                    this.updateFamilyMembers('sonsCount', 'sonsNames')(relative);
                },

                updateDaughtersNames(relative) {
                    this.updateFamilyMembers('daughtersCount', 'daughtersNames')(relative);
                },

                updateGrandsonsNames(relative) {
                    this.updateFamilyMembers('grandsonsCount', 'grandsonsNames')(relative);
                },

                updateNames(heirCategory) {
                    const newCount = heirCategory.count;
                    heirCategory.names = Array.from({
                        length: newCount
                    }, (_, i) => {
                        const existing = heirCategory.names[i] || {};
                        return {
                            name: existing.name || '',
                            hasSons: existing.hasSons || 'no',
                            sonsCount: existing.sonsCount || 0,
                            sonsNames: existing.sonsNames || [],
                            hasGrandsons: existing.hasGrandsons || 'no',
                            grandsonsCount: existing.grandsonsCount || 0,
                            grandsonsNames: existing.grandsonsNames || [],
                            daughtersCount: existing.daughtersCount || 0,
                            daughtersNames: existing.daughtersNames || []
                        };
                    });
                },

                // ===== FORMATTING METHODS =====
                getBengaliOrdinal(number) {
                    const ordinals = {
                        1: '১ম জন',
                        2: '২য় জন',
                        3: '৩য় জন',
                        4: '৪র্থ জন',
                        5: '৫ম জন',
                        6: '৬ষ্ঠ জন',
                        7: '৭ম জন',
                        8: '৮ম জন',
                        9: '৯ম জন',
                        10: '১০ম জন',
                        11: '১১তম জন',
                        12: '১২তম জন',
                        13: '১৩তম জন',
                        14: '১৪তম জন',
                        15: '১৫তম জন',
                        16: '১৬তম জন',
                        17: '১৭তম জন',
                        18: '১৮তম জন',
                        19: '১৯তম জন',
                        20: '২০তম জন'
                    };
                    return ordinals[number] || `${number}তম জন`;
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    const formattedDate = date.toLocaleDateString('bn-BD', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    return Utils.toBengaliNumerals(formattedDate);
                },

                formatTime(timeString) {
                    if (!timeString) return '';
                    const [hours, minutes] = timeString.split(':');
                    let [hour, period] = [parseInt(hours), 'AM'];
                    if (hour >= 12) {
                        period = 'PM';
                        hour = hour > 12 ? hour - 12 : hour;
                    }
                    const formattedTime = `${hour === 0 ? 12 : hour}:${minutes} ${period}`;
                    return Utils.toBengaliNumerals(formattedTime);
                },

                replaceDeceasedName(text) {
                    if (!this.formData.deceasedInfo.name) return text;

                    const name = this.formData.deceasedInfo.name;

                    if (text.includes('এর')) {
                        return text.replace(/মৃত ব্যক্তির\s*এর?/g, `${name}-এর`);
                    } else {
                        return text.replace(/মৃত ব্যক্তির/g, `${name}-এর`);
                    }
                },

                // ===== FAMILY STATUS METHODS =====
                isRelativeDisabled(key) {
                    const dependencyMap = {
                        maternalHalfBrother: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather'],
                        maternalHalfSister: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather'],
                        paternalHalfBrother: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather',
                            'brothersCount'
                        ],
                        paternalHalfSister: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather',
                            'brothersCount'
                        ],
                        paternalUncle: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather',
                            'brothersCount'
                        ],
                        paternalHalfUncle: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather',
                            'brothersCount', 'hasPaternalUncle'
                        ],
                        paternalCousin: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather',
                            'brothersCount', 'hasPaternalUncle', 'hasHalfPaternalUncle'
                        ],
                        paternalHalfCousin: ['hasSons', 'hasDeceasedSonsChildren', 'hasFatherOrGrandfather',
                            'brothersCount', 'hasPaternalUncle', 'hasHalfPaternalUncle',
                            'paternalCousinCount'
                        ]
                    };
                    const status = this.getFamilyStatus();
                    return dependencyMap[key]?.some(condition =>
                        condition.endsWith('Count') ? status[condition] > 0 : status[condition]
                    );
                },

                getFamilyStatus() {
                    return {
                        hasSons: (this.formData.heirs.children.aliveSons?.count || 0) > 0,
                        hasDeceasedSonsChildren: this.formData.heirs.deceasedSonsSons > 0 || this.formData.heirs
                            .deceasedSonsDaughters > 0,
                        hasFatherOrGrandfather: this.formData.heirs.aliveParentStatus.father.status === 'alive' ||
                            this.formData.heirs.aliveGrandParentStatus.paternalGrandFather.status === 'alive',
                        brothersCount: this.formData.heirs.siblings.brothers.count,
                        paternalCousinCount: this.formData.heirs.otherRelatives.paternalCousin.count,
                        hasPaternalUncle: this.formData.heirs.otherRelatives.paternalUncle.count > 0,
                        hasPaternalHalfUncle: this.formData.heirs.otherRelatives.paternalHalfUncle.count > 0
                    };
                },

                // ===== NAVIGATION METHODS =====
                nextStep() {
                    if (this.activeTab < this.buttons.length - 1) this.activeTab++;
                },

                prevStep() {
                    if (this.activeTab > 0) this.activeTab--;
                },

                // ===== LABEL GENERATION METHODS =====
                getBanglaNumberLabel(count, type) {
                    const labelCategories = {
                        aliveSons: this.generateNumberLabels('ছেলে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        aliveDaughters: this.generateNumberLabels('মেয়ে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        deceasedSons: this.generateNumberLabels('ছেলে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        deceasedDaughters: this.generateNumberLabels('মেয়ে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        deceasedSonsSon: this.generateNumberLabels('ছেলে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        deceasedSonsDaughter: this.generateNumberLabels('মেয়ে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        deceasedDaughtersSon: this.generateNumberLabels('ছেলে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        deceasedDaughtersDaughter: this.generateNumberLabels('মেয়ে', CONSTANTS
                            .MAX_FAMILY_MEMBERS),
                        brothers: this.generateNumberLabels('ভাই', CONSTANTS.MAX_FAMILY_MEMBERS),
                        sons: this.generateNumberLabels('ছেলে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        daughters: this.generateNumberLabels('মেয়ে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        grandsons: this.generateNumberLabels('ছেলের ছেলে', CONSTANTS.MAX_FAMILY_MEMBERS),
                        sisters: this.generateNumberLabels('বোন', CONSTANTS.MAX_FAMILY_MEMBERS),
                        maternalHalfBrother: this.generateNumberLabels('বৈপিত্রেয় ভাই', CONSTANTS
                            .MAX_FAMILY_MEMBERS),
                        maternalHalfSister: this.generateNumberLabels('বৈপিত্রেয় বোন', CONSTANTS
                            .MAX_FAMILY_MEMBERS),
                        paternalHalfBrother: this.generateNumberLabels('বৈমাতৃয় ভাই', CONSTANTS
                            .MAX_FAMILY_MEMBERS),
                        paternalHalfSister: this.generateNumberLabels('বৈমাতৃয় বোন', CONSTANTS
                            .MAX_FAMILY_MEMBERS),
                        paternalUncle: this.generateNumberLabels('চাচা', CONSTANTS.MAX_FAMILY_MEMBERS),
                        paternalHalfUncle: this.generateNumberLabels('বৈমাতৃয় চাচা', CONSTANTS
                            .MAX_FAMILY_MEMBERS),
                        paternalCousin: this.generateNumberLabels('চাচাতো ভাই', CONSTANTS.MAX_FAMILY_MEMBERS),
                        paternalHalfCousin: this.generateNumberLabels('বৈমাতৃয় চাচাতো ভাই', CONSTANTS
                            .MAX_FAMILY_MEMBERS)
                    };
                    return labelCategories[type]?.[count] || `${count} ${this.getBaseLabel(type)}`;
                },

                getBaseLabel(type) {
                    const labelMap = {
                        aliveSons: 'ছেলে',
                        aliveDaughters: 'মেয়ে',
                        brothers: 'ভাই',
                        sisters: 'বোন',
                        grandsons: 'ছেলের ছেলে',
                        paternalCousin: 'চাচাতো ভাই'
                    };
                    return labelMap[type] || type.split('s')[0];
                },

                generateNumberLabels(base, max) {
                    return Array.from({
                            length: max + 1
                        }, (_, i) =>
                        i === 0 ? `${base} নেই` : `${this.numberToBengali(i)} ${base}`
                    );
                },

                numberToBengali(num) {
                    const bengaliNumbers = ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '১০', '১১', '১২', '১৩',
                        '১৪', '১৫', '১৬', '১৭', '১৮', '১৯', '২০'
                    ];
                    return bengaliNumbers[num - 1] || num;
                },

                // ===== INPUT HANDLING METHODS =====
                convertToBengali(event, field) {
                    let value = event.target.value;
                    const englishValue = Utils.toEnglishNumerals(value);
                    const cleanValue = englishValue.replace(/[^\d.]/g, '');
                    const bengaliValue = Utils.toBengaliNumerals(cleanValue);

                    field.value = bengaliValue;
                    field.numericValue = parseFloat(cleanValue) || 0;
                },

                handleFocus(event, field) {
                    if (field.value === '০') {
                        field.value = '';
                    }
                    field.showHint = true;
                },

                handleBlur(event, field) {
                    if (!field.value || field.value.trim() === '') {
                        field.value = '০';
                        field.numericValue = 0;
                    }
                    field.showHint = false;
                },

                // Gets current date in YYYY-MM-DD format
                getCurrentDate() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                // Gets current time in HH:MM format
                getCurrentTime() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    return `${hours}:${minutes}`;
                },






                // Handles date blur event
                handleDateBlur(event, fieldName) {
                    // Set default if field is empty
                    if (!this.formData.deceasedInfo[fieldName] || this.formData.deceasedInfo[fieldName].trim() ===
                        '') {
                        this.formData.deceasedInfo[fieldName] = this.getCurrentDateBengali();
                    }
                },

                // Handles time focus event
                handleTimeFocus(event, fieldName) {
                    // Clear field if it's empty
                    if (!this.formData.deceasedInfo[fieldName] || this.formData.deceasedInfo[fieldName] === '') {
                        this.formData.deceasedInfo[fieldName] = '';
                    }
                },

                // Handles time blur event
                handleTimeBlur(event, fieldName) {
                    // Set default if field is empty
                    if (!this.formData.deceasedInfo[fieldName] || this.formData.deceasedInfo[fieldName].trim() ===
                        '') {
                        this.formData.deceasedInfo[fieldName] = this.getCurrentTimeBengali();
                    }
                },

                // Processes form data by replacing labels with deceased person's name
                getProcessedFormData() {
                    const processedData = JSON.parse(JSON.stringify(this.formData));
                    const deceasedName = this.formData.deceasedInfo.name || 'মৃত ব্যক্তির';
                    const replaceLabels = (obj) => {
                        if (typeof obj !== 'object' || obj === null) return;
                        for (const key in obj) {
                            if (key === 'label' && typeof obj[key] === 'string') {
                                // Use the same logic as replaceDeceasedName to avoid multiple "এর"
                                if (obj[key].includes('এর')) {
                                    obj[key] = obj[key].replace(/মৃত ব্যক্তির\s*এর?/g, `${deceasedName}-এর`);
                                } else {
                                    obj[key] = obj[key].replace(/মৃত ব্যক্তির/g, `${deceasedName}-এর`);
                                }
                            }
                            if (typeof obj[key] === 'object') replaceLabels(obj[key]);
                        }
                    };
                    replaceLabels(processedData);
                    return processedData;
                },

                // Debounced save to sessionStorage to improve performance
                debouncedSave() {
                    if (this.saveTimeout) {
                        clearTimeout(this.saveTimeout);
                    }
                    this.saveTimeout = setTimeout(() => {
                        try {
                            sessionStorage.setItem('calculator_data', JSON.stringify(this.formData));
                        } catch (e) {
                            console.warn('Failed to save to sessionStorage:', e);
                        }
                    }, 500); // Save after 500ms of inactivity
                },

                // Submits the form data to the server and handles responses
                submitForm() {
                    const formData = this.getProcessedFormData();
                    this.formData.version = this.formData.version ? this.formData.version + 1 : 1;
                    axios.post('/calculate-distribution', formData)
                        .then(response => {
                            console.log(response.data);
                            if (response.data.redirect_url) window.location.href = response.data.redirect_url;
                        })
                        .catch(error => {
                            console.error('Error:', error.response.data);
                            alert('একটি ত্রুটি ঘটেছে! দয়া করে আবার চেষ্টা করুন।');
                        });
                }
            },
            watch: {
                // Optimized: Only watch specific fields instead of deep watching entire formData
                'formData.deceasedInfo': {
                    handler(newVal) {
                        this.debouncedSave();
                    },
                    deep: true
                },
                'formData.assets': {
                    handler(newVal) {
                        this.debouncedSave();
                    },
                    deep: true
                },
                'formData.heirs': {
                    handler(newVal) {
                        this.debouncedSave();
                    },
                    deep: true
                },
                'formData.heirs.spouseWives.count': {
                    handler: 'updateWifeNames',
                    immediate: true
                }
            }
        }).mount("#calculator");
    </script>
@endsection
