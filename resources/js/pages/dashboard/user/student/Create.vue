<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Group, type Shift, type Classe } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { List } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useForm } from '@inertiajs/vue3';
import { computed, defineProps, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User ( Student )',
        href: route('students.index'),
    },
];

const props = defineProps<{
    classes: Classe[];
    groups: Group[];
    shifts: Shift[];
}>();

const form = useForm<{
    name: string;
    email: string;
    date_of_birth?: string;
    gender?: string;
    religion?: string;
    phone?: string;
    class_id: number | null;
    group_id: number | null;
    shift_id?: number | null;
    father_name?: string;
    mother_name?: string;
    guardian_name?: string;
    guardian_relation?: string;
    guardian_email?: string;
    guardian_phone?: string;
    guardian_address?: string;
    present_address?: string;
    password: string;
    status?: string;
}>({
    name: '',
    email: '',
    date_of_birth: '',
    gender: '',
    religion: '',
    phone: '',
    class_id: null,
    group_id: null,
    shift_id: null,
    father_name: '',
    mother_name: '',
    guardian_name: '',
    guardian_relation: '',
    guardian_email: '',
    guardian_phone: '',
    guardian_address: '',
    present_address: '',
    password: '',
    status: 'Active',
});

const filteredGroups = computed(() => {
    if (!form.class_id) return [];
    return props.groups.filter(group => group.class_id === form.class_id);
});

watch(() => form.class_id, (newClassId) => {
    const isValidGroup = props.groups.some(
        (group) => group.id === form.group_id && group.class_id === newClassId
    );

    if (!isValidGroup) {
        form.group_id = null;
    }
});

const submit = () => {
    form.post(route('students.store'), {
            onSuccess: () => {
                toast.success('User created successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localUsers.value = props.users.map(user => ({ ...user }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error creating user', {
                        description: errors.error,
                    });
                }
            },
        });
};
</script>

<template>
    <Head title="User ( Student )" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('students.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <List />
                </Link>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="p-4">
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" type="text" v-model="form.name" placeholder="Name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" placeholder="Email" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="date_of_birth">Date of Birth</Label>
                            <Input id="date_of_birth" type="date" v-model="form.date_of_birth" :max="new Date().toISOString().split('T')[0]" />
                            <InputError :message="form.errors.date_of_birth" />
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
                            <Label for="phone">Phone</Label>
                            <Input id="phone" class="mt-1 block w-full" v-model="form.phone" placeholder="Phone" />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="present_address">Present Address</Label>
                            <Textarea v-model="form.present_address" placeholder="Enter your present address" />
                            <InputError class="mt-2" :message="form.errors.present_address" />
                        </div>

                        <div v-if="!editingUser" class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input id="password" type="password" v-model="form.password" placeholder="Password" />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="class_id">Class</Label>
                            <Select id="class_id" v-model="form.class_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a class" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Classes</SelectLabel>
                                        <SelectItem v-for="classe in classes" :key="classe.id" :value="classe.id">
                                            {{ classe.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.class_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="group_id">Group</Label>
                            <Select
                                id="group_id"
                                v-model="form.group_id"
                                :disabled="!form.class_id || filteredGroups.length === 0"
                            >
                                <SelectTrigger class="w-full">
                                    <SelectValue
                                        :placeholder="!form.class_id ? 'Select class first' : (filteredGroups.length === 0 ? 'No groups available' : 'Select a group')"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup v-if="filteredGroups.length">
                                        <SelectLabel>Groups</SelectLabel>
                                        <SelectItem
                                            v-for="group in filteredGroups"
                                            :key="group.id"
                                            :value="group.id"
                                        >
                                            {{ group.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.group_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="shift_id">Shift</Label>
                            <Select id="shift_id" v-model="form.shift_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a shift" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Shifts</SelectLabel>
                                        <SelectItem v-for="shift in shifts" :key="shift.id" :value="shift.id">
                                            {{ shift.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.shift_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="father_name">Father Name</Label>
                            <Input id="father_name" type="text" v-model="form.father_name" placeholder="Father Name" />
                            <InputError class="mt-2" :message="form.errors.father_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="mother_name">Mother Name</Label>
                            <Input id="mother_name" type="text" v-model="form.mother_name" placeholder="Mother Name" />
                            <InputError class="mt-2" :message="form.errors.mother_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="guardian_name">Guardian Name</Label>
                            <Input id="guardian_name" type="text" v-model="form.guardian_name" placeholder="Guardian Name" />
                            <InputError class="mt-2" :message="form.errors.guardian_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="guardian_relation">Guardian Relation</Label>
                            <Input id="guardian_relation" type="text" v-model="form.guardian_relation" placeholder="Guardian Relation" />
                            <InputError class="mt-2" :message="form.errors.guardian_relation" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="guardian_email">Guardian Email</Label>
                            <Input id="guardian_email" type="email" v-model="form.guardian_email" placeholder="Guardian Email" />
                            <InputError class="mt-2" :message="form.errors.guardian_email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="guardian_phone">Guardian Phone</Label>
                            <Input id="guardian_phone" type="text" v-model="form.guardian_phone" placeholder="Guardian Phone" />
                            <InputError class="mt-2" :message="form.errors.guardian_phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="guardian_address">Guardian Address</Label>
                            <Textarea v-model="form.guardian_address" placeholder="Enter guardian address" />
                            <InputError class="mt-2" :message="form.errors.guardian_address" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Status</SelectLabel>
                                        <SelectItem value="Active">Active</SelectItem>
                                        <SelectItem value="Inactive">Inactive</SelectItem>
                                        <SelectItem value="Suspended">Suspended</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            {{ editingUser ? 'Edit' : 'Create' }} User ( Student )
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
