<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Plus, Trash, Edit } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ColumnDef } from '@tanstack/vue-table';
import DataTableHeader from '@/components/data-table/DataTableHeader.vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button';
import DataTable from '../../../../components/data-table/DataTable.vue';
import { Dialog, DialogClose, DialogDescription, DialogFooter, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useForm } from '@inertiajs/vue3';
import { h, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User ( Student )',
        href: route('students.index'),
    },
];

const props = defineProps<{
    users: User[];
}>();

const localUsers = ref(props.users.map(user => ({
    ...user,
})));

const deletingIds = ref<number[]>([]);

const confirmDelete = (userId: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        deletingIds.value.push(userId);

        router.delete(route('users.destroy', userId), {
            preserveScroll: true,
            onSuccess: () => {
                localUsers.value = localUsers.value.filter(
                    (item) => item.id !== userId
                );
                toast.success('User deleted successfully', {
                    description: new Date().toLocaleString(),
                });
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error deleting User', {
                        description: errors.error,
                    });
                }
            },
            onFinish: () => {
                deletingIds.value = deletingIds.value.filter(id => id !== userId);
            },
        });
    }
};

const columns: ColumnDef<User>[] = [
    {
        id: 'select',
        header: ({ table }) => h(Checkbox, {
            'modelValue': table.getIsAllPageRowsSelected(),
            'onUpdate:modelValue': (value: boolean | "indeterminate") => table.toggleAllPageRowsSelected(value === true),
            'ariaLabel': 'Select all',
        }),
        cell: ({ row }) => h(Checkbox, {
            'modelValue': row.getIsSelected(),
            'onUpdate:modelValue': (value: boolean | "indeterminate") => row.toggleSelected(value === true),
            'ariaLabel': 'Select row',
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        id: 'id',
        accessorKey: 'id',
        header: ({ column }) => h(DataTableHeader, { column, title: 'ID' }),
        cell: ({ row }) => row.original.id,
    },
    {
        id: 'name',
        accessorKey: 'name',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Name' }),
        cell: ({ row }) => row.original.name,
    },
    {
        id: 'email',
        accessorKey: 'email',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Email' }),
        cell: ({ row }) => row.original.email,
    },
    {
        id: 'status',
        accessorKey: 'status',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Status' }),
        cell: ({ row }) => {
            const status = row.original.status;
            return h('span', {
                class: `inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${status === 'Active' ? 'bg-green-100 text-green-800' : status === 'Inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}`
            }, status);
        },
    },
    {
        id: 'actions',
        accessorKey: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const user = row.original;
            const isDeleting = deletingIds.value.includes(user.id);

            return h('div', { class: 'flex items-center gap-2' }, [
                h(Button, {
                    variant: 'outline',
                    onClick: () => openEditDialog(user),
                }, [
                    h(Edit, { class: 'h-4 w-4' })
                ]),
                h(Button, {
                    variant: 'destructive',
                    disabled: isDeleting,
                    onClick: () => confirmDelete(user.id),
                }, [
                    isDeleting ? h('span', { class: 'text-sm' }, '...') : h(Trash, { class: 'h-5 w-5' }),
                ])
            ]);
        }
    },
];

const form = useForm<{
    name: string;
    email: string;
    date_of_birth?: string;
    gender?: string;
    religion?: string;
    phone?: string;
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
    present_address: '',
    password: '',
    status: 'Active',
});

const editingUser= ref<User | null>(null);
const dialogOpen = ref(false);

const openCreateDialog = () => {
    form.reset();
    form.clearErrors();
    editingUser.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (user: User) => {
    form.name = user.name;
    form.email = user.email;
    form.date_of_birth = user.date_of_birth ?? '';
    form.gender = user.gender ?? '';
    form.religion = user.religion ?? '';
    form.phone = user.phone != null ? String(user.phone) : '';
    form.present_address = user.present_address ?? '';
    form.status = user.status;
    form.clearErrors();
    editingUser.value = user;
    dialogOpen.value = true;
};

const submit = () => {
    if (editingUser.value) {
        form.put(route('students.update', editingUser.value.id), {
            onSuccess: () => {
                toast.success('User updated successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localUsers.value = props.users.map(user => ({ ...user }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error updating User', {
                        description: errors.error,
                    });
                }
            },
        });
    } else {
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
    }
};
</script>

<template>
    <Head title="User ( Student )" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button @click="openCreateDialog" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" /> New User ( Student )
                        </Button>
                    </DialogTrigger>
                    <DialogScrollContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>{{ editingUser ? 'Edit' : 'Create' }} User ( Student )</DialogTitle>
                            <DialogDescription>
                                {{ editingUser ? 'Edit' : 'Create' }} an user to manage your users effectively.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="submit" class="p-4">
                            <div class="grid gap-6">
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
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button type="button" variant="secondary">
                                    Close
                                </Button>
                            </DialogClose>
                        </DialogFooter>
                    </DialogScrollContent>
                </Dialog>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <DataTable :columns="columns" :data="localUsers" :searchable-columns="['name', 'email']" :filterable-columns="['status']" />
            </div>
        </div>
    </AppLayout>
</template>
