<script setup lang="ts">
import { ref, h } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type ExpenseCategory } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Trash, Edit } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ColumnDef } from '@tanstack/vue-table';
import DataTableColumnHeader from '@/components/data-table/DataTableColumnHeader.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import DataTable from '../../../components/data-table/DataTable.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Expense Category',
        href: '/expense-categories',
    },
];

const props = defineProps<{
    expenseCategories: ExpenseCategory[];
}>();

const localExpenseCategories = ref([...props.expenseCategories]);
const deletingIds = ref<number[]>([]);

const grayText = (text: string | number) => h('span', { class: 'text-gray-500' }, text);

const confirmDelete = (expenseCategoryId: number) => {
    if (confirm('Are you sure you want to delete this expense category?')) {
        deletingIds.value.push(expenseCategoryId);

        router.delete(route('expense-categories.destroy', expenseCategoryId), {
            preserveScroll: true,
            onSuccess: () => {
                localExpenseCategories.value = localExpenseCategories.value.filter(
                (item) => item.id !== expenseCategoryId
                );
                toast.success('Expense category deleted successfully', {
                description: new Date().toLocaleString(),
                });
            },
            onError: (errors) => {
                if (errors.error) {
                toast.error('Error deleting expense category', {
                    description: errors.error,
                });
                }
            },
            onFinish: () => {
                deletingIds.value = deletingIds.value.filter(id => id !== expenseCategoryId);
            },
        });
    }
};

const columns: ColumnDef<ExpenseCategory>[] = [
    {
        id: 'select',
        header: ({ table }) =>
        h(Checkbox, {
            modelValue: table.getIsAllPageRowsSelected(),
            'onUpdate:modelValue': (value: boolean) =>
            table.toggleAllPageRowsSelected(!!value),
            'ariaLabel': 'Select all',
        }),
        cell: ({ row }) =>
        h(Checkbox, {
            modelValue: row.getIsSelected(),
            'onUpdate:modelValue': (value: boolean) =>
            row.toggleSelected(!!value),
            'ariaLabel': 'Select row',
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        id: 'id',
        accessorKey: 'id',
        header: ({ column }) =>
        h(DataTableColumnHeader, {
            column,
            title: 'ID',
        }),
        cell: ({ row }) => grayText(row.original.id),
    },
    {
        id: 'name',
        accessorKey: 'name',
        header: ({ column }) =>
        h(DataTableColumnHeader, {
            column,
            title: 'Name',
        }),
        cell: ({ row }) => grayText(row.original.name),
    },
    {
        id: 'status',
        accessorKey: 'status',
        header: ({ column }) =>
        h(DataTableColumnHeader, {
            column,
            title: 'Status',
        }),
        cell: ({ row }) => grayText(row.original.status),
    },
    {
        id: 'actions',
        accessorKey: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const expenseCategory = row.original;
            const isDeleting = deletingIds.value.includes(expenseCategory.id);

            return h('div', { class: 'flex items-center gap-2' }, [
                h(Link, {
                    href: route('expense-categories.edit', expenseCategory.id),
                    class: 'text-blue-500 hover:text-blue-700',
                    }, [
                    h(Edit, { class: 'h-5 w-5' }),
                ]),
                h(Button, {
                    variant: 'destructive',
                    disabled: isDeleting,
                    onClick: () => confirmDelete(expenseCategory.id),
                    }, [
                    isDeleting ? h('span', { class: 'text-sm' }, '...') : h(Trash, { class: 'h-5 w-5' }),
                ]),
            ]);
        },
    },
];
</script>

<template>
    <Head title="Expense Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link
                :href="route('expense-categories.create')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                <Plus class="h-5 w-5" />
                </Link>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <DataTable :columns="columns" :data="localExpenseCategories" :searchable-columns="['name']"/>
            </div>
        </div>
    </AppLayout>
</template>
