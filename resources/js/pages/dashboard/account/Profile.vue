<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { cn } from '@/lib/utils'
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/account/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { DateFormatter, getLocalTimeZone, parseDate, today, isSameDay } from '@internationalized/date'
import { CalendarIcon } from 'lucide-vue-next'
import { Textarea } from '@/components/ui/textarea'
import { toast } from 'vue-sonner'

import { CalendarCell, CalendarCellTrigger, CalendarGrid, CalendarGridBody, CalendarGridHead, CalendarGridRow, CalendarHeadCell, CalendarHeader, CalendarHeading } from '@/components/ui/calendar'
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useVModel } from '@vueuse/core'
import { CalendarRoot, type CalendarRootEmits, type CalendarRootProps, useDateFormatter, useForwardPropsEmits } from 'reka-ui'
import { createDecade, createYear, toDate } from 'reka-ui/date'
import { type HTMLAttributes } from 'vue'

interface ProfileForm {
    profile_photo: File | null;
    name: string;
    email: string;
    gender: string | null;
    date_of_birth: string | null;
    blood_group: string | null;
    religion: string | null;
    marital_status: string | null;
    phone: string | null;
    present_address: string | null;
    permanent_address: string | null;
    _method: string;
}

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const calendarProps = withDefaults(defineProps<CalendarRootProps & { class?: HTMLAttributes['class'] }>(), {
    modelValue: undefined,
    placeholder() {
        return today(getLocalTimeZone())
    },
    weekdayFormat: 'short',
});

const calendarEmits = defineEmits<CalendarRootEmits>();

const delegatedProps = computed(() => {
    const { class: _, placeholder: __, ...delegated } = calendarProps;
    return delegated;
});

const dateValue = useVModel(calendarProps, 'modelValue', calendarEmits, {
    passive: true,
    defaultValue: today(getLocalTimeZone()),
});

const forwarded = useForwardPropsEmits(delegatedProps, calendarEmits);
const formatter = useDateFormatter('en');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const existingProfilePhotoUrl = ref<string | null>(user.profile_photo ? `/uploads/profile_photos/${user.profile_photo}` : null);
const newProfilePhotoFile = ref<File | null>(null);
const newProfilePhotoPreviewUrl = ref<string | null>(null);

watch(newProfilePhotoFile, (newFile, oldFile) => {
    if (oldFile && newProfilePhotoPreviewUrl.value) {
        URL.revokeObjectURL(newProfilePhotoPreviewUrl.value);
        newProfilePhotoPreviewUrl.value = null;
    }

    if (newFile) {
        newProfilePhotoPreviewUrl.value = URL.createObjectURL(newFile);
    }
}, { immediate: false });

onUnmounted(() => {
    if (newProfilePhotoPreviewUrl.value) {
        URL.revokeObjectURL(newProfilePhotoPreviewUrl.value);
    }
});

const form = useForm<ProfileForm>({
    profile_photo: null,
    name: user.name,
    email: user.email,
    gender: user.gender,
    date_of_birth: user.date_of_birth,
    blood_group: user.blood_group,
    religion: user.religion,
    marital_status: user.marital_status,
    phone: user.phone,
    present_address: user.present_address,
    permanent_address: user.permanent_address,
    _method: 'PATCH',
});

onMounted(() => {
    if (user.date_of_birth) {
        try {
            dateValue.value = parseDate(user.date_of_birth, getLocalTimeZone());
        } catch (e) {
            console.error("Failed to parse user's date of birth:", user.date_of_birth, e);
            dateValue.value = today(getLocalTimeZone());
        }
    } else {
        dateValue.value = today(getLocalTimeZone());
    }
});


const onProfilePhotoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    newProfilePhotoFile.value = file || null;
};

const submit = () => {
    form.date_of_birth = dateValue.value ? dateValue.value.toString() : null;

    form.profile_photo = newProfilePhotoFile.value;

    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            if (newProfilePhotoPreviewUrl.value) {
                URL.revokeObjectURL(newProfilePhotoPreviewUrl.value);
            }
            newProfilePhotoPreviewUrl.value = null;
            newProfilePhotoFile.value = null;

            const fileInput = document.getElementById('profile_photo') as HTMLInputElement;
            if (fileInput) {
                fileInput.value = '';
            }

            toast.success('Profile updated successfully', {
                description: new Date().toLocaleString(),
            });
            existingProfilePhotoUrl.value = form.profile_photo ? `/uploads/profile_photos/${form.profile_photo}` : null;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address and other details" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="profile_photo">Profile Photo</Label>
                        <Input id="profile_photo" type="file" class="mt-1 block w-full" @change="onProfilePhotoChange" accept="image/*" />
                        <div class="flex space-x-2">
                            <img v-if="existingProfilePhotoUrl" :src="existingProfilePhotoUrl" class="w-20 h-20 rounded-full object-cover" alt="Current Profile Photo" />
                            <div v-else class="w-20 h-20 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <img v-if="newProfilePhotoPreviewUrl" :src="newProfilePhotoPreviewUrl" class="w-20 h-20 rounded-full object-cover" alt="New Profile Photo Preview" />
                        </div>
                        <InputError class="mt-2" :message="form.errors.profile_photo" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required placeholder="Email address" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="gender">Gender</Label>
                        <RadioGroup v-model="form.gender" class="grid grid-cols-3 gap-4 mt-1">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="male" value="Male" />
                                <Label for="male">Male</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="female" value="Female" />
                                <Label for="female">Female</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem id="other" value="Other" />
                                <Label for="other">Other</Label>
                            </div>
                        </RadioGroup>
                        <InputError class="mt-2" :message="form.errors.gender" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="blood_group">Blood Group</Label>
                        <Select v-model="form.blood_group"> <SelectTrigger>
                                <SelectValue placeholder="Select a blood group" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Blood Group</SelectLabel>
                                    <SelectItem value="A+">A+</SelectItem>
                                    <SelectItem value="A-">A-</SelectItem>
                                    <SelectItem value="B+">B+</SelectItem>
                                    <SelectItem value="B-">B-</SelectItem>
                                    <SelectItem value="AB+">AB+</SelectItem>
                                    <SelectItem value="AB-">AB-</SelectItem>
                                    <SelectItem value="O+">O+</SelectItem>
                                    <SelectItem value="O-">O-</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.blood_group" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="date_of_birth">Date Of Birth</Label>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button
                                    variant="outline"
                                    :class="cn('w-full justify-start text-left font-normal', !dateValue && 'text-muted-foreground')"
                                >
                                    <CalendarIcon class="mr-2 h-4 w-4" />
                                    {{ dateValue ? df.format(dateValue.toDate(getLocalTimeZone())) : "Pick a date" }}
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto p-0">
                                <CalendarRoot
                                    v-slot="{ date, grid, weekDays }"
                                    v-model="dateValue"
                                    v-bind="forwarded"
                                    class="rounded-md border p-3"
                                    :max-value="today(getLocalTimeZone())" >
                                    <CalendarHeader>
                                        <CalendarHeading class="flex w-full items-center justify-between gap-2">
                                            <Select
                                                :model-value="dateValue?.month.toString()"
                                                @update:model-value="(v) => {
                                                    if (!v || !dateValue) return;
                                                    try {
                                                        let newDate = dateValue.set({ month: Number(v) });
                                                        if (newDate.compare(today(getLocalTimeZone())) > 0) {
                                                            newDate = today(getLocalTimeZone());
                                                        }
                                                        dateValue = newDate;
                                                    } catch (e) {
                                                        dateValue = dateValue.set({ month: Number(v) });
                                                    }
                                                }"
                                            >
                                                <SelectTrigger aria-label="Select month" class="w-[60%]">
                                                    <SelectValue placeholder="Select month" />
                                                </SelectTrigger>
                                                <SelectContent class="max-h-[200px]">
                                                    <SelectItem
                                                        v-for="month in createYear({ dateObj: date })"
                                                        :key="month.toString()"
                                                        :value="month.month.toString()"
                                                    >
                                                    {{ formatter.custom(toDate(month), { month: 'long' }) }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>

                                            <Select
                                                :model-value="dateValue?.year.toString()"
                                                @update:model-value="(v) => {
                                                    if (!v || !dateValue) return;
                                                    try {
                                                        let newDate = dateValue.set({ year: Number(v) });
                                                        if (newDate.compare(today(getLocalTimeZone())) > 0) {
                                                            newDate = today(getLocalTimeZone());
                                                        }
                                                        dateValue = newDate;
                                                    } catch (e) {
                                                        dateValue = dateValue.set({ year: Number(v) });
                                                    }
                                                }"
                                            >
                                                <SelectTrigger aria-label="Select year" class="w-[40%]">
                                                    <SelectValue placeholder="Select year" />
                                                </SelectTrigger>
                                                <SelectContent class="max-h-[200px]">
                                                    <SelectItem
                                                        v-for="yearValue in createDecade({ dateObj: date, startIndex: -100, endIndex: 0 })" :key="yearValue.toString()"
                                                        :value="yearValue.year.toString()"
                                                    >
                                                        {{ yearValue.year }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </CalendarHeading>
                                    </CalendarHeader>

                                    <div class="flex flex-col space-y-4 pt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
                                        <CalendarGrid v-for="month in grid" :key="month.value.toString()">
                                            <CalendarGridHead>
                                                <CalendarGridRow>
                                                    <CalendarHeadCell
                                                        v-for="day in weekDays" :key="day"
                                                    >
                                                        {{ day }}
                                                    </CalendarHeadCell>
                                                </CalendarGridRow>
                                            </CalendarGridHead>
                                            <CalendarGridBody class="grid">
                                                <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
                                                    <CalendarCell
                                                        v-for="weekDate in weekDates"
                                                        :key="weekDate.toString()"
                                                        :date="weekDate"
                                                        :is-disabled="weekDate.compare(today(getLocalTimeZone())) > 0" >
                                                        <CalendarCellTrigger
                                                            :day="weekDate"
                                                            :month="month.value"
                                                            :is-selected="dateValue ? isSameDay(weekDate, dateValue) : false" />
                                                    </CalendarCell>
                                                </CalendarGridRow>
                                            </CalendarGridBody>
                                        </CalendarGrid>
                                    </div>
                                </CalendarRoot>
                            </PopoverContent>
                        </Popover>
                        <InputError class="mt-2" :message="form.errors.date_of_birth" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="religion">Religion</Label>
                        <Select v-model="form.religion">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a religion" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Religion</SelectLabel>
                                    <SelectItem value="Islam">Islam</SelectItem>
                                    <SelectItem value="Hinduism">Hinduism</SelectItem>
                                    <SelectItem value="Christianity">Christianity</SelectItem>
                                    <SelectItem value="Buddhism">Buddhism</SelectItem>
                                    <SelectItem value="Other">Other</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.religion" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="marital_status">Marital Status</Label>
                        <Select v-model="form.marital_status">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a marital status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Marital Status</SelectLabel>
                                    <SelectItem value="Single">Single</SelectItem>
                                    <SelectItem value="Married">Married</SelectItem>
                                    <SelectItem value="Divorced">Divorced</SelectItem>
                                    <SelectItem value="Widowed">Widowed</SelectItem>
                                    <SelectItem value="Separated">Separated</SelectItem>
                                    <SelectItem value="Other">Other</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.marital_status" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone">Phone</Label>
                        <Input id="phone" class="mt-1 block w-full" v-model="form.phone" placeholder="Phone" />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="present_address">Present Address</Label>
                        <Textarea v-model="form.present_address" placeholder="Enter your present address" />
                        <InputError class="mt-2" :message="form.errors.present_address" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="permanent_address">Permanent Address</Label>
                        <Textarea v-model="form.permanent_address" placeholder="Enter your permanent address" />
                        <InputError class="mt-2" :message="form.errors.permanent_address" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">Save</Button>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
