<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Role, type Designation } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
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
import { List } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User ( Employee )',
        href: route('employees.index'),
    },
];

defineProps<{
    roles: Role[];
    designations: Designation[];
}>();

const form = useForm<{
    role_ids: string[];
    name: string;
    national_id_no?: string;
    joining_date?: string;
    type?: string;
    email: string;
    designation_id?: string;
    date_of_birth?: string;
    gender?: string;
    religion?: string;
    phone?: string;
    present_address?: string;
    password: string;
    status?: string;
    resignation_date?: string;
    retirement_date?: string;
    suspension_date?: string;
    resignation_reason?: string;
    suspension_reason?: string;
}>({
    role_ids: [],
    name: '',
    email: '',
    national_id_no: '',
    joining_date: '',
    type: 'Full Time',
    designation_id: '',
    date_of_birth: '',
    gender: '',
    religion: '',
    phone: '',
    present_address: '',
    password: '',
    status: 'Running',
    resignation_date: '',
    retirement_date: '',
    suspension_date: '',
    resignation_reason: '',
    suspension_reason: '',
});

const submit = () => {
    form.post(route('employees.store'), {
            onSuccess: () => {
                toast.success('User created successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
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
    <Head title="User ( Employee )" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('employees.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <List />
                </Link>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="p-4">
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="grid gap-2">
                            <Label>Roles</Label>
                            <Select v-model="form.role_ids" multiple>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select roles" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Roles</SelectLabel>
                                        <SelectItem v-for="role in roles" :key="role.id" :value="role.id.toString()" :disabled="['Admin', 'Super Admin', 'Student', 'Guardian'].includes(role.name)">
                                            {{ role.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.role_ids" />
                        </div>

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
                            <Label for="national_id_no">National ID No</Label>
                            <Input id="national_id_no" type="text" v-model="form.national_id_no" placeholder="National ID No" />
                            <InputError :message="form.errors.national_id_no" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="joining_date">Joining Date</Label>
                            <Input id="joining_date" type="date" v-model="form.joining_date" :max="new Date().toISOString().split('T')[0]" />
                            <InputError :message="form.errors.joining_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Type</Label>
                            <Select v-model="form.type">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Type</SelectLabel>
                                        <SelectItem value="Full Time">Full Time</SelectItem>
                                        <SelectItem value="Part Time">Part Time</SelectItem>
                                        <SelectItem value="Contractual">Contractual</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.type" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Designation</Label>
                            <Select v-model="form.designation_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select designation" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Designation</SelectLabel>
                                        <SelectItem v-for="designation in designations" :key="designation.id" :value="designation.id.toString()">
                                            {{ designation.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.designation_id" />
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

                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input id="password" type="password" v-model="form.password" placeholder="Password" />
                            <InputError :message="form.errors.password" />
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
                                        <SelectItem value="Running">Running</SelectItem>
                                        <SelectItem value="Resigned">Resigned</SelectItem>
                                        <SelectItem value="Retired">Retired</SelectItem>
                                        <SelectItem value="Suspended">Suspended</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div v-if="form.status === 'Resigned'" class="grid gap-2">
                            <Label for="resignation_date">Resignation Date</Label>
                            <Input id="resignation_date" type="date" v-model="form.resignation_date" :max="new Date().toISOString().split('T')[0]" />
                            <InputError :message="form.errors.resignation_date" />
                        </div>

                        <div v-if="form.status === 'Retired'" class="grid gap-2">
                            <Label for="retirement_date">Retirement Date</Label>
                            <Input id="retirement_date" type="date" v-model="form.retirement_date" :max="new Date().toISOString().split('T')[0]" />
                            <InputError :message="form.errors.retirement_date" />
                        </div>

                        <div v-if="form.status === 'Suspended'" class="grid gap-2">
                            <Label for="suspension_date">Suspension Date</Label>
                            <Input id="suspension_date" type="date" v-model="form.suspension_date" :max="new Date().toISOString().split('T')[0]" />
                            <InputError :message="form.errors.suspension_date" />
                        </div>

                        <div v-if="form.status === 'Resigned'" class="grid gap-2">
                            <Label for="resignation_reason">Resignation Reason</Label>
                            <Textarea id="resignation_reason" v-model="form.resignation_reason" placeholder="Enter resignation reason" />
                            <InputError :message="form.errors.resignation_reason" />
                        </div>

                        <div v-if="form.status === 'Suspended'" class="grid gap-2">
                            <Label for="suspension_reason">Suspension Reason</Label>
                            <Textarea id="suspension_reason" v-model="form.suspension_reason" placeholder="Enter suspension reason" />
                            <InputError :message="form.errors.suspension_reason" />
                        </div>

                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Create User ( Employee )
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
