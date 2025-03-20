<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { FlexRender, useVueTable, getCoreRowModel, getPaginationRowModel, getFilteredRowModel , getSortedRowModel } from '@tanstack/vue-table'

import { toast } from 'vue-sonner'

import { ArrowUpDown, ChevronDown, Edit, Trash } from 'lucide-vue-next'
import { h, ref, computed, onMounted, watch } from 'vue'

import type { ColumnDef, ColumnFiltersState, SortingState, VisibilityState } from '@tanstack/vue-table'
import { valueUpdater } from '@/lib/utils'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

interface User {
    id: string;
    name: string;
    email: string;
    avatar: string;
}
const users = defineProps<{
    users: User[];
}>();

interface Flash {
    success?: null;
    error?: null;
}

onMounted(() => {
    watch(() => usePage<{ flash: Flash }>().props.flash,
    (flash: Flash) => {
        if (flash.success) {
            toast.success(flash.success);
            flash.success = null;
        }
    }, { immediate: true });
});

// Reactive data
const data = ref(users.users);

// Table columns
const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) => h('div', row.getValue('id')),
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]);
        },
        cell: ({ row }) => h('div', row.getValue('name')),
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]);
        },
        cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
    },
    {
        accessorKey: 'avatar',
        header: 'Avatar',
        cell: ({ row }) => {
            const avatarUrl = row.getValue('avatar');
            return avatarUrl
                ? h('img', { src: avatarUrl, alt: row.getValue('name'), class: 'h-8 w-8 rounded-full' })
                : h('div', 'No Avatar');
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            return h('div', { class: 'flex gap-2' }, [
                // Edit button with icon using Link component
                h(Link, { href: `/users/${row.getValue('id')}/edit`, class: 'btn btn-indigo-500 dark:bg-indigo-600 px-2 py-1 rounded' }, () => [
                    h(Edit, { class: 'h-4 w-4' }),
                ]),
                // Delete button with icon using Button component
                h(Button, { variant: 'outline', class: 'btn btn-red-500 dark:bg-red-600 px-2 py-1 rounded', onClick: () => handleDelete(row.getValue('id')) }, () => [
                    h(Trash, { class: 'h-4 w-4' }),
                ]),
            ]);
        },
    },
];

// Handle delete action
const handleDelete = (id: string) => {
    if (confirm('Are you sure you want to delete this user?')) {
        const response = axios.delete(`/users/${id}`);
        if (response.status === 204) {
            toast.success('User deleted successfully.');
            data.value = data.value.filter((user) => user.id !== id);
        } else {
            toast.error('Failed to delete user.');
        }
    }
};
// Table state
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});

// Initialize table
const table = useVueTable({
    data: data.value,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: (updater) => valueUpdater(updater, sorting),
    onColumnFiltersChange: (updater) => valueUpdater(updater, columnFilters),
    onColumnVisibilityChange: (updater) => valueUpdater(updater, columnVisibility),
    onRowSelectionChange: (updater) => valueUpdater(updater, rowSelection),
    state: {
        get sorting() { return sorting.value; },
        get columnFilters() { return columnFilters.value; },
        get columnVisibility() { return columnVisibility.value; },
        get rowSelection() { return rowSelection.value; },
    },
});


// Computed property for visible columns
const visibleColumns = computed(() => {
    return table.getAllColumns().filter((column) => column.getCanHide());
});

// Function to toggle all columns visibility
const toggleAllColumnsVisibility = (visible: boolean) => {
    table.getAllColumns().forEach((column) => {
        if (column.getCanHide()) {
            column.toggleVisibility(visible);
        }
    });
};

// Computed property to check if all columns are hidden
const areAllColumnsHidden = computed(() => {
    return visibleColumns.value.every((column) => !column.getIsVisible());
});

// Computed property for pagination text
const paginationText = computed(() => {
    const start = table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1;
    const end = Math.min(start + table.getState().pagination.pageSize - 1, table.getFilteredRowModel().rows.length);
    return `Showing ${start} to ${end} of ${table.getFilteredRowModel().rows.length} rows`;
});

// Row quantity options
const rowQuantityOptions = [10, 25, 50, 100, { label: 'All', value: -1 }];

// Function to update the number of rows per page
const updateRowsPerPage = (value: number) => {
    table.setPageSize(value === -1 ? table.getFilteredRowModel().rows.length : value);
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-end">
                <Link href="/users/create" class="btn btn-indigo-500 dark:bg-indigo-600 px-2 py-1 rounded">Create User</Link>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="w-full p-5">
                    <div class="flex gap-2 items-center py-4">
                        <Input class="max-w-sm" placeholder="Search emails..." :model-value="table.getColumn('email')?.getFilterValue() as string" @update:model-value="table.getColumn('email')?.setFilterValue($event)" />
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="ml-auto">
                                    Columns <ChevronDown class="ml-2 h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <div class="flex gap-2 p-2">
                                    <Button variant="outline" size="sm" @click="toggleAllColumnsVisibility(true)">
                                        Show All
                                    </Button>
                                    <Button variant="outline" size="sm" @click="toggleAllColumnsVisibility(false)">
                                        Hide All
                                    </Button>
                                </div>
                                <DropdownMenuCheckboxItem
                                    v-for="column in visibleColumns"
                                    :key="column.id"
                                    class="capitalize"
                                    :checked="column.getIsVisible()"
                                    @update:checked="(value) => column.toggleVisibility(!!value)"
                                >
                                    {{ column.id }}
                                </DropdownMenuCheckboxItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                                    <TableHead v-for="header in headerGroup.headers" :key="header.id">
                                        <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="table.getRowModel().rows?.length">
                                    <template v-if="areAllColumnsHidden">
                                        <TableRow>
                                            <TableCell :colspan="columns.length" class="h-24 text-center">
                                                All columns are hidden.
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <template v-else>
                                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id" :data-state="row.getIsSelected() && 'selected'">
                                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                </template>
                                <TableRow v-else>
                                    <TableCell :colspan="columns.length" class="h-24 text-center">
                                        No results.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="flex items-center justify-end space-x-2 py-4">
                        <div class="flex-1 text-sm text-muted-foreground">
                            {{ paginationText }}
                        </div>
                        <div class="space-x-2">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" class="ml-auto">
                                        Rows per page: {{ table.getState().pagination.pageSize === -1 ? 'All' : table.getState().pagination.pageSize }}
                                        <ChevronDown class="ml-2 h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuCheckboxItem
                                        v-for="option in rowQuantityOptions"
                                        :key="typeof option === 'object' ? option.label : option"
                                        :checked="table.getState().pagination.pageSize === (typeof option === 'object' ? option.value : option)"
                                        @update:checked="updateRowsPerPage(typeof option === 'object' ? option.value : option)"
                                    >
                                        {{ typeof option === 'object' ? option.label : option }}
                                    </DropdownMenuCheckboxItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                            <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                                Previous
                            </Button>
                            <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                                Next
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
