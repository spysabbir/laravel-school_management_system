<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, router, Link } from '@inertiajs/vue3';
import { Plus, Trash, Edit } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ColumnDef } from '@tanstack/vue-table';
import DataTableHeader from '@/components/data-table/DataTableHeader.vue';
import { Button } from '@/components/ui/button';
import DataTable from '../../../../components/data-table/DataTable.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { h, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User ( Employee )',
        href: route('employees.index'),
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
        id: 'roles',
        accessorKey: 'roles',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Roles' }),
        cell: ({ row }) => {
            const roles = row.original.roles.map(role => role.name).join(', ');
            return h('span', { class: 'text-sm text-muted-foreground' }, roles);
        },
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
                h(Link, {
                    href: route('employees.edit', user.id),
                    class: 'inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800',
                }, [
                    h(Edit, { class: 'h-5 w-5' }),
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
</script>

<template>
    <Head title="User ( Employee )" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('employees.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <Plus class="h-5 w-5" /> New User ( Employee )
                </Link>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <DataTable :columns="columns" :data="localUsers" :searchable-columns="['name', 'email']" :filterable-columns="['status']" />
            </div>
        </div>
    </AppLayout>
</template>
