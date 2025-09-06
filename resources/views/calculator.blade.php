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
                            <label class="block text-sm font-semibold text-[#006F45]">মৃত ব্যক্তির নাম:</label>
                            <input type="text" v-model="formData.deceasedInfo.name"
                                class="w-full p-2 text-sm border-2 border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                        </div>
                        <!-- Gender Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">মৃত ব্যক্তির লিঙ্গ:</label>
                            <div class="border-2 border-gray-300 rounded-lg p-3 md:p-4 bg-white">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <label class="flex items-center space-x-3 cursor-pointer group">
                                        <input type="radio" value="male" v-model="formData.deceasedInfo.gender"
                                            class="h-5 w-5 text-[#006F45] border-2 border-gray-300 focus:ring-0 focus:border-[#006F45] rounded-full transition-all">
                                        <span
                                            class="text-black group-hover:text-[#006F45] text-sm md:text-base">পুরুষ</span>
                                    </label>
                                    <label class="flex items-center space-x-3 cursor-pointer group">
                                        <input type="radio" value="female" v-model="formData.deceasedInfo.gender"
                                            class="h-5 w-5 text-green-600 border-2 border-green-300 focus:ring-0 focus:border-green-400 rounded-full transition-all">
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
                                class="w-full p-2 text-sm border-2 border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                        </div>
                        <!-- Death Time Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">মৃত্যুর সময়:</label>
                            <input type="time" v-model="formData.deceasedInfo.deathTime"
                                class="w-full p-2 text-sm border-2 border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                        </div>
                        <!-- Marital Status Input -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-[#006F45]">বৈবাহিক অবস্থা:</label>
                            <select v-model="formData.deceasedInfo.maritalStatus"
                                class="w-full p-2 text-sm border-2 border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                    <div v-for="(field, key) in formData.assets" :key="key" class="space-y-2">
                        <!-- Asset Label and Placeholder -->
                        <label class="block text-sm font-semibold text-[#006F45]">@{{ field.label }}:</label>
                        <span class="text-sm whitespace-nowrap">(@{{ field.placeholder }})</span>
                        <div class="flex items-center gap-2">
                            <!-- Asset Value Input -->
                            <input type="number" v-model="field.value"
                                class="w-full p-2 text-sm border-2 border-gray-300 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                            class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                            <div class="flex flex-col md:flex-row items-start md:items-center gap-2 mb-3">
                                <label class="block text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(relation.label) }}:</label>
                                <div class="flex gap-3">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="alive" v-model="relation.status"
                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                        <span class="text-black">জীবিত</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="dead" v-model="relation.status"
                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                        <span class="text-black">মৃত</span>
                                    </label>
                                </div>
                            </div>
                            <div v-if="relation.status === 'alive'" class="w-full mt-2">
                                <input type="text" v-model="relation.name"
                                    :placeholder="`${replaceDeceasedName(relation.label)}-এর নাম`"
                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                            </div>
                        </div>
                    </div>
                    <!-- Spouse Section: Input for wives (if deceased is male) or husband (if deceased is female) -->
                    <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                            <label class="w-full md:w-1/3 text-sm font-semibold text-[#006F45]">
                                @{{ formData.deceasedInfo.gender === 'male' ? replaceDeceasedName('মৃত ব্যক্তির বর্তমানে জীবিত স্ত্রীর সংখ্যা') : replaceDeceasedName('মৃত ব্যক্তির স্বামীর অবস্থা') }}:
                            </label>
                            <div v-if="formData.deceasedInfo.gender === 'male'" class="w-full md:w-2/3 space-y-3">
                                <select v-model="formData.heirs.spouseWives.count"
                                    :disabled="formData.deceasedInfo.maritalStatus !== 'married'"
                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 disabled:cursor-not-allowed">
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
                                        class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                </div>
                            </div>
                            <div v-else class="w-full md:w-2/3 space-y-3">
                                <div class="flex gap-4">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="alive" v-model="formData.heirs.spouseStatus"
                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                        <span class="text-black">জীবিত</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="dead" v-model="formData.heirs.spouseStatus"
                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                        <span class="text-black">মৃত</span>
                                    </label>
                                </div>
                                <div v-if="formData.heirs.spouseStatus === 'alive'">
                                    <input type="text" v-model="formData.heirs.spouseName" placeholder="স্বামীর নাম"
                                        class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template v-for="(child, key) in formData.heirs.children">
                                <div v-if="!['deceasedSons', 'deceasedDaughters'].includes(key)" :key="key"
                                    class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-3">
                                        <label class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(child.label) }}:</label>
                                        <select v-model="child.count" @change="updateNames(child)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                            <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, key) }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <!-- v-for: Input for each alive child's name -->
                                        <div v-for="(member, index) in child.names" :key="index">
                                            <input type="text" v-model="member.name"
                                                :placeholder="`${replaceDeceasedName(child.label)} ${getBengaliOrdinal(index + 1)} এর নাম`"
                                                class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                            <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                                <div class="mb-4 pb-2 border-b border-green-200">
                                    <h3 class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName('মৃত ব্যক্তির মৃত ছেলে') }}</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-4">
                                        <label class="text-sm font-medium text-black">মৃত ছেলের সংখ্যা:</label>
                                        <select v-model="formData.heirs.children.deceasedSons.count"
                                            @change="updateNames(formData.heirs.children.deceasedSons)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                            </div>
                                            <!-- Input for descendants of deceased son -->
                                            <div class="bg-[#F5FFE8] p-4 rounded-lg space-y-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">ছেলের
                                                            সংখ্যা:</label>
                                                        <select v-model="son.sonsCount" @change="updateSonsNames(son)"
                                                            class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">মেয়ের
                                                            সংখ্যা:</label>
                                                        <select v-model="son.daughtersCount"
                                                            @change="updateDaughtersNames(son)"
                                                            class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                            <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                                <div class="mb-4 pb-2 border-b border-green-200">
                                    <h3 class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName('মৃত ব্যক্তির মৃত মেয়ে') }}</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-4">
                                        <label class="text-sm font-medium text-black">মৃত মেয়ের সংখ্যা:</label>
                                        <select v-model="formData.heirs.children.deceasedDaughters.count"
                                            @change="updateNames(formData.heirs.children.deceasedDaughters)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                            </div>
                                            <div class="bg-[#F5FFE8] p-4 rounded-lg space-y-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">ছেলের
                                                            সংখ্যা:</label>
                                                        <select v-model="daughter.sonsCount"
                                                            @change="updateSonsNames(daughter)"
                                                            class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'sons') }}</option>
                                                        </select>
                                                        <div v-if="daughter.sonsCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(grandson, gIndex) in daughter.sonsNames"
                                                                :key="gIndex">
                                                                <input type="text" v-model="grandson.name"
                                                                    :placeholder="`${daughter.name || 'মৃত মেয়ে'}-এর ছেলে ${getBengaliOrdinal(gIndex+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-sm font-medium text-black">মেয়ের
                                                            সংখ্যা:</label>
                                                        <select v-model="daughter.daughtersCount"
                                                            @change="updateDaughtersNames(daughter)"
                                                            class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                                            <option v-for="n in 21" :value="n - 1">
                                                                @{{ getBanglaNumberLabel(n - 1, 'daughters') }}</option>
                                                        </select>
                                                        <div v-if="daughter.daughtersCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(granddaughter, dIndex) in daughter.daughtersNames"
                                                                :key="dIndex">
                                                                <input type="text" v-model="granddaughter.name"
                                                                    :placeholder="`${daughter.name || 'মৃত মেয়ে'}-এর মেয়ে ${getBengaliOrdinal(dIndex+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                        <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                            <div class="space-y-4">
                                <div v-for="(sibling, key) in formData.heirs.siblings" :key="key"
                                    class="space-y-4 pb-4 border-b border-green-200 last:border-0">
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                        <label
                                            class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(sibling.label) }}:</label>
                                        <select v-model="sibling.count" @change="updateNames(sibling)"
                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                            <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, key) }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <div v-for="(member, index) in sibling.names" :key="index">
                                            <input type="text" v-model="member.name"
                                                :placeholder="`${replaceDeceasedName(sibling.label)} ${getBengaliOrdinal(index + 1)} এর নাম`"
                                                class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                                        <span class="text-black">হ্যাঁ</span>
                                                    </label>
                                                    <label class="flex items-center space-x-2">
                                                        <input type="radio" value="no" v-model="sibling.hasSons"
                                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                                        <span class="text-black">না</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div v-if="sibling.hasSons === 'yes'" class="space-y-4">
                                                <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                                    <label
                                                        class="text-sm font-medium text-black">@{{ replaceDeceasedName('ছেলের সংখ্যা') }}:</label>
                                                    <select v-model="sibling.sonsCount" @change="updateSonsNames(sibling)"
                                                        class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                                        <option v-for="n in 21" :value="n - 1">
                                                            @{{ getBanglaNumberLabel(n - 1, 'sons') }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="sibling.sonsCount > 0" class="space-y-2 ml-2">
                                                    <div v-for="(son, index) in sibling.sonsNames" :key="index">
                                                        <input type="text" v-model="son.name"
                                                            :placeholder="`সহোদর ভাই এর ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                            class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                                                    class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                                                <span class="text-black">হ্যাঁ</span>
                                                            </label>
                                                            <label class="flex items-center space-x-2">
                                                                <input type="radio" value="no"
                                                                    v-model="sibling.hasGrandsons"
                                                                    class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
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
                                                                class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
                                                                <option v-for="n in 21" :value="n - 1">
                                                                    @{{ getBanglaNumberLabel(n - 1, 'grandsons') }}</option>
                                                            </select>
                                                        </div>
                                                        <div v-if="sibling.grandsonsCount > 0" class="space-y-2 ml-2">
                                                            <div v-for="(grandson, index) in sibling.grandsonsNames"
                                                                :key="index">
                                                                <input type="text" v-model="grandson.name"
                                                                    :placeholder="`সহোদর ভাই এর ছেলের ছেলে ${getBengaliOrdinal(index+1)} এর নাম`"
                                                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                                    class="w-full md:w-1/3 text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(relation.label) }}:</label>
                                <div class="flex gap-3">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="alive" v-model="relation.status"
                                            :disabled="(key === 'maternalGrandMother' && formData.heirs.aliveParentStatus.mother
                                                .status === 'alive') || (['paternalGrandFather',
                                                    'paternalGrandMother'
                                                ].includes(key) && formData.heirs.aliveParentStatus.father
                                                .status === 'alive')"
                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45] disabled:opacity-50">
                                        <span class="text-black">জীবিত</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" value="dead" v-model="relation.status"
                                            :disabled="(key === 'maternalGrandMother' && formData.heirs.aliveParentStatus.mother
                                                .status === 'alive') || (['paternalGrandFather',
                                                    'paternalGrandMother'
                                                ].includes(key) && formData.heirs.aliveParentStatus.father
                                                .status === 'alive')"
                                            class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45] disabled:opacity-50">
                                        <span class="text-black">মৃত</span>
                                    </label>
                                </div>
                            </div>
                            <div v-if="relation.status === 'alive'" class="w-full">
                                <input type="text" v-model="relation.name"
                                    :placeholder="`${replaceDeceasedName(relation.label)}-এর নাম`"
                                    class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200">
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
                    <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                        <div class="space-y-6">
                            <div v-for="(relative, key) in formData.heirs.otherRelatives" :key="key"
                                class="space-y-4 pb-4 border-b border-green-200 last:border-0">
                                <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                    <label class="text-sm font-semibold text-[#006F45]">@{{ replaceDeceasedName(relative.label) }}:</label>
                                    <select v-model="relative.count" @change="updateNames(relative)"
                                        :disabled="isRelativeDisabled(key)"
                                        class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100">
                                        <option v-for="n in 21" :value="n - 1">@{{ getBanglaNumberLabel(n - 1, key) }}</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <div v-for="(member, index) in relative.names" :key="index">
                                        <input type="text" v-model="member.name" :disabled="isRelativeDisabled(key)"
                                            :placeholder="`${replaceDeceasedName(relative.label)} ${getBengaliOrdinal(index + 1)} এর নাম`"
                                            class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100">
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
                                                                class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                                            <span class="text-black">হ্যাঁ</span>
                                                        </label>
                                                        <label class="flex items-center space-x-2">
                                                            <input type="radio" value="no"
                                                                v-model="relative.hasSons"
                                                                :disabled="isRelativeDisabled(key)"
                                                                class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
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
                                                            class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100">
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
                                                                class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100">
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
                                                                        class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
                                                                    <span class="text-black">হ্যাঁ</span>
                                                                </label>
                                                                <label class="flex items-center space-x-2">
                                                                    <input type="radio" value="no"
                                                                        v-model="relative.hasGrandsons"
                                                                        :disabled="isRelativeDisabled(key)"
                                                                        class="h-4 w-4 text-[#006F45] border-green-300 focus:ring-[#006F45]">
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
                                                                    class="w-full md:w-1/2 px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100">
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
                                                                        class="w-full px-3 py-2 text-sm border border-green-200 rounded-lg focus:border-[#006F45] focus:ring-2 focus:ring-green-200 disabled:bg-gray-100">
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
                <div class="bg-[#F5FFE8] p-4 rounded-lg border border-green-200">
                    <h3 class="text-lg font-semibold text-[#006F45] mb-4">এক নজরে সমস্ত তথ্য</h3>
                    <!-- Deceased Person Info Summary: Shows name, gender, death date/time, marital status -->
                    <div class="mb-6 bg-white p-4 rounded shadow">
                        <h4 class="font-semibold text-[#006F45] mb-3">মৃত ব্যক্তির তথ্য:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <p><span class="font-medium">নাম:</span> @{{ formData.deceasedInfo.name || 'নাম দেওয়া হয়নি' }}</p>
                            <p><span class="font-medium">লিঙ্গ:</span> @{{ formData.deceasedInfo.gender === 'male' ? 'পুরুষ' : 'নারী' }}</p>
                            <p><span class="font-medium">মৃত্যুর তারিখ:</span> @{{ formatDate(formData.deceasedInfo.deathDate) || 'তারিখ দেওয়া হয়নি' }}</p>
                            <p><span class="font-medium">মৃত্যুর সময়:</span> @{{ formatTime(formData.deceasedInfo.deathTime) || 'সময় দেওয়া হয়নি' }}</p>
                            <p><span class="font-medium">বৈবাহিক অবস্থা:</span> @{{ formData.deceasedInfo.maritalStatus === 'married' ? 'বিবাহিত' : (formData.deceasedInfo.maritalStatus === 'unmarried' ? 'অবিবাহিত' : 'তালাকপ্রাপ্ত') }}</p>
                        </div>
                    </div>
                    <!-- Asset Info Summary: Shows all entered assets using v-for -->
                    <div class="mb-6 bg-white p-4 rounded shadow">
                        <h4 class="font-semibold text-[#006F45] mb-3">সম্পত্তির বিবরণ:</h4>
                        <!-- List all assets with label and value -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div v-for="(asset, key) in formData.assets" :key="key">
                                <span class="font-medium">@{{ asset.label }}:</span> @{{ asset.value || '0' }}
                                @{{ asset.placeholder ? `(${asset.placeholder})` : '' }}
                            </div>
                        </div>
                    </div>
                    <!-- Heirs Info Summary: Shows parents, siblings, and other relatives using v-for and conditionals -->
                    <div class="bg-white p-4 rounded shadow">
                        <h4 class="font-semibold text-[#006F45] mb-3">ওয়ারিশদের তথ্য:</h4>
                        <div class="mb-4">
                            <h5 class="font-medium text-[#006F45]">পিতা-মাতা:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div v-for="parent in formData.heirs.aliveParentStatus" :key="parent.label">
                                    <p><span class="font-medium">@{{ replaceDeceasedName(parent.label) }}:</span> @{{ parent.status === 'alive' ? parent.name || replaceDeceasedName(parent.label) : 'মৃত' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-medium text-[#006F45]">সহধর্মিণী/স্বামী:</h5>
                            <div v-if="formData.deceasedInfo.gender === 'male'">
                                <p>স্ত্রীর সংখ্যা: @{{ formData.heirs.spouseWives.count }}</p>
                                <div v-for="(wife, index) in formData.heirs.spouseWives.names" :key="index">
                                    @{{ wife.name || `${getBengaliOrdinal(index + 1)} স্ত্রীর নাম` }}
                                </div>
                            </div>
                            <div v-else>
                                <p>স্বামীর অবস্থা: @{{ formData.heirs.spouseStatus === 'alive' ? formData.heirs.spouseName || 'স্বামী' : 'মৃত' }}</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-medium text-[#006F45]">সন্তান:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="childType in ['aliveSons', 'aliveDaughters', 'deceasedSons', 'deceasedDaughters']"
                                    :key="childType">
                                    <div v-if="formData.heirs.children[childType].count > 0">
                                        <p class="font-medium">@{{ replaceDeceasedName(formData.heirs.children[childType].label) }}: @{{ formData.heirs.children[childType].count }} জন</p>
                                        <div v-for="(child, index) in formData.heirs.children[childType].names"
                                            :key="index" class="ml-4 border-l-2 border-green-200 pl-2">
                                            <p class="mt-2">@{{ child.name || `${replaceDeceasedName('মৃত সন্তান')} ${getBengaliOrdinal(index+1)}` }}</p>
                                            <div v-if="childType.includes('deceased') && child.sonsNames.length > 0"
                                                class="ml-3 mt-1">
                                                <p class="text-sm font-medium">ছেলে (@{{ child.sonsNames.length }} জন):</p>
                                                <div v-for="(son, sIndex) in child.sonsNames" :key="sIndex">
                                                    <p class="text-sm">@{{ sIndex + 1 }}. @{{ son.name || 'নামহীন' }}</p>
                                                </div>
                                            </div>
                                            <div v-if="childType.includes('deceased') && child.daughtersNames.length > 0"
                                                class="ml-3 mt-1">
                                                <p class="text-sm font-medium">মেয়ে (@{{ child.daughtersNames.length }} জন):</p>
                                                <div v-for="(daughter, dIndex) in child.daughtersNames"
                                                    :key="dIndex">
                                                    <p class="text-sm">@{{ dIndex + 1 }}. @{{ daughter.name || 'নামহীন' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-medium text-[#006F45]">ভাইবোন:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="siblingType in ['brothers', 'sisters']" :key="siblingType">
                                    <div v-if="formData.heirs.siblings[siblingType].count > 0">
                                        <p class="font-medium">@{{ replaceDeceasedName(formData.heirs.siblings[siblingType].label) }}: @{{ formData.heirs.siblings[siblingType].count }} জন</p>
                                        <div v-for="(sibling, index) in formData.heirs.siblings[siblingType].names"
                                            :key="index" class="ml-4 border-l-2 border-green-200 pl-3">
                                            <div class="mb-2">@{{ sibling.name || `${getBengaliOrdinal(index + 1)} এর নাম` }}</div>
                                            <div v-if="siblingType === 'brothers' && sibling.sonsNames.length > 0"
                                                class="bg-[#F5FFE8] p-3 rounded-lg">
                                                <p class="text-sm font-medium mb-2">@{{ replaceDeceasedName('ছেলে') }}
                                                    (@{{ sibling.sonsNames.length }} জন):</p>
                                                <div v-for="(son, sIndex) in sibling.sonsNames" :key="sIndex"
                                                    class="ml-3">
                                                    @{{ son.name || `${getBengaliOrdinal(sIndex + 1)} ছেলে` }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else-if="siblingType === 'brothers'">
                                        <div v-if="formData.heirs.siblings.brothers.hasSons === 'yes'">
                                            <p class="font-medium">@{{ replaceDeceasedName('মৃত সহোদর ভাই এর ছেলেরা') }}:</p>
                                            <div v-for="(son, index) in formData.heirs.siblings.brothers.sonsNames"
                                                :key="index" class="ml-4 border-l-2 border-green-200 pl-3">
                                                @{{ son.name || `${getBengaliOrdinal(index + 1)} ছেলে` }}
                                            </div>
                                            <div
                                                v-if="formData.heirs.siblings.brothers.sonsCount === 0 && formData.heirs.siblings.brothers.hasGrandsons === 'yes'">
                                                <p class="font-medium mt-2">@{{ replaceDeceasedName('মৃত সহোদর ভাই এর ছেলের ছেলেরা') }}:</p>
                                                <div v-for="(grandson, index) in formData.heirs.siblings.brothers.grandsonsNames"
                                                    :key="index" class="ml-4 border-l-2 border-green-200 pl-3">
                                                    @{{ grandson.name || `${getBengaliOrdinal(index + 1)} ছেলের ছেলে` }}
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-gray-600">@{{ replaceDeceasedName('কোন সহোদর ভাই নেই') }}</div>
                                    </div>
                                    <div
                                        v-else-if="siblingType === 'sisters' && formData.heirs.siblings.sisters.count === 0">
                                        <p class="text-gray-600">@{{ replaceDeceasedName('কোন সহোদর বোন নেই') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-medium text-[#006F45]">অন্যান্য আত্মীয়:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="(relative, key) in formData.heirs.otherRelatives" :key="key">
                                    <div
                                        v-if="(relative.count > 0 || relative.hasSons === 'yes') && !isRelativeDisabled(key)">
                                        <p class="font-medium">@{{ replaceDeceasedName(relative.label) }}: <span
                                                v-if="relative.count > 0">@{{ relative.count }} জন</span><span
                                                v-else>মৃত</span></p>
                                        <div v-if="relative.count > 0" class="space-y-2">
                                            <div v-for="(member, index) in relative.names" :key="index">
                                                @{{ member.name || `${getBengaliOrdinal(index + 1)} এর নাম` }}
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div v-if="relative.hasSons === 'yes'"
                                                class="ml-4 border-l-2 border-green-200 pl-3 mt-2">
                                                <p class="font-medium text-sm">ছেলেরা:</p>
                                                <div v-for="(son, index) in relative.sonsNames" :key="index"
                                                    class="mt-1">
                                                    @{{ son.name || `${getBengaliOrdinal(index + 1)} ছেলে` }}
                                                </div>
                                                <div v-if="relative.sonsCount === 0 && relative.hasGrandsons === 'yes'"
                                                    class="ml-4 border-l-2 border-green-200 pl-3 mt-2">
                                                    <p class="font-medium text-sm">ছেলের ছেলেরা:</p>
                                                    <div v-for="(grandson, index) in relative.grandsonsNames"
                                                        :key="index" class="mt-1">
                                                        @{{ grandson.name || `${getBengaliOrdinal(index + 1)} ছেলের ছেলে` }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-medium text-[#006F45]">দাদা-দাদি-নানি:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-for="grandparent in formData.heirs.aliveGrandParentStatus"
                                    :key="grandparent.label">
                                    <p><span class="font-medium">@{{ replaceDeceasedName(grandparent.label) }}:</span> @{{ grandparent.status === 'alive' ? grandparent.name || replaceDeceasedName(grandparent.label) : 'মৃত' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-center">
                        <button @click="submitForm"
                            class="px-8 py-3 bg-[#006F45] text-white rounded-lg font-medium hover:bg-[#03442C] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-200 focus:ring-offset-2 text-lg">
                            বন্টন গণনা করুন
                        </button>
                    </div>
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        Vue.createApp({
            mounted() {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                    .content;
                if (!Object.keys(this.formData).length) {
                    const storedData = sessionStorage.getItem('calculator_data');
                    if (storedData) {
                        try {
                            this.formData = this.mergeWithDefaults(JSON.parse(storedData));
                        } catch (e) {
                            console.error('Error parsing stored data:', e);
                        }
                    }
                }
            },
            data() {
                const initialDataEl = document.getElementById('calculator');
                let initialData = {};
                if (initialDataEl) {
                    try {
                        initialData = JSON.parse(initialDataEl.dataset.initial || '{}') || {};
                        console.log('Parsed initialData:', initialData);
                    } catch (e) {
                        console.error('Error parsing initial data:', e);
                        initialData = {};
                    }
                }
                return {
                    relativeConfigs: [
                        this.createRelativeConfig('paternalHalfBrother',
                            'মৃত ব্যক্তির বৈমাতৃয় ভাই (মা ভিন্ন, বাবা এক) এর কোন ছেলে আছে?', 'বৈমাতৃয় ভাই'),
                        this.createRelativeConfig('paternalCousin', 'মৃত ব্যক্তির চাচাতো ভাই এর কোন ছেলে আছে?',
                            'চাচাতো ভাই'),
                        this.createRelativeConfig('paternalHalfCousin',
                            'মৃত ব্যক্তির বৈমাতৃয় (মা ভিন্ন, বাবা এক) চাচাতো ভাই এর কোন ছেলে আছে?',
                            'বৈমাতৃয় চাচাতো ভাই')
                    ],
                    activeTab: 0,
                    buttons: [{
                        label: "মৃত ব্যক্তির তথ্য"
                    }, {
                        label: "মৃত ব্যক্তির সম্পত্তি"
                    }, {
                        label: "মৃত ব্যক্তির ওয়ারিশ"
                    }, {
                        label: "এক নজরে"
                    }],
                    formData: this.mergeWithDefaults(initialData),
                };
            },
            methods: {
                // Validates the form data structure and required fields
                validateFormData(data) {
                    if (!data.deceasedInfo || !data.heirs) {
                        console.error('Invalid initial data format');
                        return false;
                    }
                    return true;
                },

                // Sanitizes form data by converting values to appropriate types
                sanitizeFormData(data) {
                    Object.values(data.assets).forEach(asset => {
                        asset.value = Number(asset.value) || 0;
                    });
                    Object.values(data.heirs.children).forEach(child => {
                        child.count = Math.max(0, parseInt(child.count));
                    });
                    return data;
                },

                // Merges initial data with default values
                mergeWithDefaults(initialData) {
                    const defaults = this.initializeFormData();
                    if (!initialData || Object.keys(initialData).length === 0) return defaults;
                    if (typeof initialData !== 'object' || Array.isArray(initialData)) return defaults;
                    if (!this.validateFormData(initialData)) {
                        console.error('Invalid initial data format');
                        return defaults;
                    }
                    const sanitized = {
                        deceasedInfo: {
                            ...defaults.deceasedInfo,
                            ...(initialData.deceasedInfo || {})
                        },
                        assets: this.mergeAssets(defaults.assets, initialData.assets),
                        heirs: this.mergeHeirs(defaults.heirs, initialData.heirs)
                    };
                    return this.sanitizeFormData(sanitized);
                },

                // Initializes the form data structure with default values
                initializeFormData() {
                    return {
                        deceasedInfo: this.createDeceasedInfo(),
                        assets: this.createAssets(),
                        heirs: this.createHeirs()
                    };
                },

                // Creates default deceased information structure
                createDeceasedInfo() {
                    return {
                        name: '',
                        deathDate: '',
                        gender: 'male',
                        deathTime: '',
                        maritalStatus: 'married'
                    };
                },

                // Creates default assets structure
                createAssets() {
                    return {
                        land: this.createAsset('জমির পরিমাণ', 'শতাংশ/কাঠা'),
                        flat: this.createAsset('ফ্ল্যাট', 'স্কয়ার ফিট'),
                        cash: this.createAsset('নগদ টাকার পরিমাণ', 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত'),
                        investment: this.createAsset('বিনিয়োগের পরিমাণ', 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত'),
                        owedCash: this.createAsset('পাওনা টাকার পরিমাণ', 'ব্যাংক/আর্থিক প্রতিষ্ঠান ভিত্তিত'),
                        unpaidDebt: this.createAsset('অপরিশোধিত ঋণ', 'টাকায়'),
                        jewellery: this.createAsset('অলংকারের পরিমাণ', 'টাকায়'),
                    };
                },

                // Creates an asset object with label and placeholder
                createAsset(label, placeholder) {
                    return {
                        label,
                        value: '',
                        placeholder
                    };
                },

                // Creates the initial heirs data structure with default values
                createHeirs() {
                    return {
                        spouseWives: {
                            count: 0,
                            names: []
                        },
                        spouseStatus: 'alive',
                        spouseName: '',
                        aliveParentStatus: {
                            father: this.createFamilyMember('মৃত ব্যক্তির বাবা'),
                            mother: this.createFamilyMember('মৃত ব্যক্তির মা')
                        },
                        aliveGrandParentStatus: {
                            paternalGrandFather: this.createFamilyMember('মৃত ব্যক্তির দাদা', 'dead'),
                            paternalGrandMother: this.createFamilyMember('মৃত ব্যক্তির দাদি', 'dead'),
                            maternalGrandMother: this.createFamilyMember('মৃত ব্যক্তির নানি', 'dead')
                        },
                        children: this.createChildren(),
                        siblings: this.createSiblings(),
                        otherRelatives: this.createOtherRelatives()
                    };
                },

                // Creates a family member object with label and status
                createFamilyMember(label, status = 'alive') {
                    return {
                        label,
                        status,
                        name: ''
                    };
                },

                // Creates the children data structure with different categories
                createChildren() {
                    return {
                        aliveSons: this.createChildCategory('মৃত ব্যক্তির জীবিত ছেলে'),
                        aliveDaughters: this.createChildCategory('মৃত ব্যক্তির জীবিত মেয়ে'),
                        deceasedSons: this.createDeceasedChildCategory('মৃত ব্যক্তির মৃত ছেলে'),
                        deceasedDaughters: this.createDeceasedChildCategory('মৃত ব্যক্তির মৃত মেয়ে')
                    };
                },

                // Creates a child category object with label and count
                createChildCategory(label) {
                    return {
                        label,
                        count: 0,
                        names: []
                    };
                },

                // Creates a deceased child category with initial entry
                createDeceasedChildCategory(label) {
                    return {
                        label,
                        count: 0,
                        names: [this.createDeceasedChildEntry()]
                    };
                },

                // Creates a deceased child entry with name and children counts
                createDeceasedChildEntry() {
                    return {
                        name: '',
                        sonsCount: 0,
                        sonsNames: [],
                        daughtersCount: 0,
                        daughtersNames: []
                    };
                },

                // Creates the siblings data structure
                createSiblings() {
                    return {
                        brothers: this.createSiblingCategory('মৃত ব্যক্তির সহোদর ভাই'),
                        sisters: this.createSiblingCategory('মৃত ব্যক্তির সহোদর বোন')
                    };
                },

                // Creates a sibling category with extended properties
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

                // Creates the other relatives data structure
                createOtherRelatives() {
                    return {
                        maternalHalfBrother: this.createRelative('মৃত ব্যক্তির বৈপিত্রেয় ভাই'),
                        maternalHalfSister: this.createRelative('মৃত ব্যক্তির বৈপিত্রেয় বোন'),
                        paternalHalfBrother: this.createRelativeWithChildren('মৃত ব্যক্তির বৈমাতৃয় ভাই'),
                        paternalHalfSister: this.createRelative('মৃত ব্যক্তির বৈমাতৃয় বোন'),
                        paternalUncle: this.createRelative('মৃত ব্যক্তির চাচা'),
                        paternalHalfUncle: this.createRelative('মৃত ব্যক্তির বৈমাতৃয় চাচা'),
                        paternalCousin: this.createRelativeWithChildren('মৃত ব্যক্তির চাচাতো ভাই'),
                        paternalHalfCousin: this.createRelativeWithChildren('মৃত ব্যক্তির বৈমাতৃয় চাচাতো ভাই')
                    };
                },

                // Creates a basic relative object
                createRelative(label) {
                    return {
                        label,
                        count: 0,
                        names: []
                    };
                },

                // Creates a relative object with children properties
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
                },

                // Creates configuration for relative with question and prefix
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

                // Creates a function to update family members count and names
                updateFamilyMembers(countKey, namesKey) {
                    return (relative) => {
                        const newCount = relative[countKey];
                        relative[namesKey] = Array.from({
                            length: newCount
                        }, (_, i) => relative[namesKey][i] || {
                            name: ''
                        });
                    };
                },

                // Updates wife names based on count
                updateWifeNames() {
                    this.updateFamilyMembers('count', 'names')(this.formData.heirs.spouseWives);
                },

                // Updates sons names for a relative
                updateSonsNames(relative) {
                    this.updateFamilyMembers('sonsCount', 'sonsNames')(relative);
                },

                // Updates daughters names for a relative
                updateDaughtersNames(relative) {
                    this.updateFamilyMembers('daughtersCount', 'daughtersNames')(relative);
                },

                // Updates grandsons names for a relative
                updateGrandsonsNames(relative) {
                    this.updateFamilyMembers('grandsonsCount', 'grandsonsNames')(relative);
                },

                // Updates names for an heir category with all related properties
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

                // Converts a number to Bengali ordinal format
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

                // Formats a date string to Bengali locale format
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('bn-BD', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                },

                // Formats a time string to 12-hour format with AM/PM
                formatTime(timeString) {
                    if (!timeString) return '';
                    const [hours, minutes] = timeString.split(':');
                    let [hour, period] = [parseInt(hours), 'AM'];
                    if (hour >= 12) {
                        period = 'PM';
                        hour = hour > 12 ? hour - 12 : hour;
                    }
                    return `${hour === 0 ? 12 : hour}:${minutes} ${period}`;
                },

                // Replaces 'মৃত ব্যক্তির' with the deceased person's name in text
                replaceDeceasedName(text) {
                    return this.formData.deceasedInfo.name ? text.replace(/মৃত ব্যক্তির/g,
                        `${this.formData.deceasedInfo.name}-এর`) : text;
                },

                // Checks if a relative should be disabled based on family status
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
                    return dependencyMap[key]?.some(condition => condition.endsWith('Count') ? status[condition] >
                        0 : status[condition]);
                },

                // Gets the current status of family members for dependency checks
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

                // Moves to the next step in the form
                nextStep() {
                    if (this.activeTab < this.buttons.length - 1) this.activeTab++;
                },

                // Moves to the previous step in the form
                prevStep() {
                    if (this.activeTab > 0) this.activeTab--;
                },

                // Gets the Bengali number label for a given count and type
                getBanglaNumberLabel(count, type) {
                    const labelCategories = {
                        aliveSons: this.generateLabels('ছেলে', 20),
                        aliveDaughters: this.generateLabels('মেয়ে', 20),
                        deceasedSons: this.generateLabels('ছেলে', 20),
                        deceasedDaughters: this.generateLabels('মেয়ে', 20),
                        deceasedSonsSon: this.generateLabels('ছেলে', 20),
                        deceasedSonsDaughter: this.generateLabels('মেয়ে', 20),
                        deceasedDaughtersSon: this.generateLabels('ছেলে', 20),
                        deceasedDaughtersDaughter: this.generateLabels('মেয়ে', 20),
                        brothers: this.generateLabels('ভাই', 20),
                        sons: this.generateLabels('ছেলে', 20),
                        daughters: this.generateLabels('মেয়ে', 20),
                        grandsons: this.generateLabels('ছেলের ছেলে', 20),
                        sisters: this.generateLabels('বোন', 20),
                        maternalHalfBrother: this.generateLabels('বৈপিত্রেয় ভাই', 20),
                        maternalHalfSister: this.generateLabels('বৈপিত্রেয় বোন', 20),
                        paternalHalfBrother: this.generateLabels('বৈমাতৃয় ভাই', 20),
                        paternalHalfSister: this.generateLabels('বৈমাতৃয় বোন', 20),
                        paternalUncle: this.generateLabels('চাচা', 20),
                        paternalHalfUncle: this.generateLabels('বৈমাতৃয় চাচা', 20),
                        paternalCousin: this.generateLabels('চাচাতো ভাই', 20),
                        paternalHalfCousin: this.generateLabels('বৈমাতৃয় চাচাতো ভাই', 20)
                    };
                    return labelCategories[type]?.[count] || `${count} ${this.getBaseLabel(type)}`;
                },

                // Gets the base label for a given type
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

                // Generates labels for a given base and maximum count
                generateLabels(base, max) {
                    return Array.from({
                        length: max + 1
                    }, (_, i) => i === 0 ? `${base} নেই` : `${this.numberToBengali(i)} ${base}`);
                },

                // Converts a number to Bengali numeral
                numberToBengali(num) {
                    const bengaliNumbers = ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '১০', '১১', '১২', '১৩',
                        '১৪', '১৫', '১৬', '১৭', '১৮', '১৯', '২০'
                    ];
                    return bengaliNumbers[num - 1] || num;
                },

                // Processes form data by replacing labels with deceased person's name
                getProcessedFormData() {
                    const processedData = JSON.parse(JSON.stringify(this.formData));
                    const deceasedName = this.formData.deceasedInfo.name || 'মৃত ব্যক্তির';
                    const replaceLabels = (obj) => {
                        if (typeof obj !== 'object' || obj === null) return;
                        for (const key in obj) {
                            if (key === 'label' && typeof obj[key] === 'string') obj[key] = obj[key].replace(
                                /মৃত ব্যক্তির/g, `${deceasedName}-এর`);
                            if (typeof obj[key] === 'object') replaceLabels(obj[key]);
                        }
                    };
                    replaceLabels(processedData);
                    return processedData;
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
                formData: {
                    handler(newVal) {
                        sessionStorage.setItem('calculator_data', JSON.stringify(newVal));
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
