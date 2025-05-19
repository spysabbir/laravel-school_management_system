<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef, SortingState, ColumnFiltersState, VisibilityState } from '@tanstack/vue-table'
import { FlexRender, getCoreRowModel, getPaginationRowModel, getSortedRowModel, getFilteredRowModel, useVueTable } from '@tanstack/vue-table'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Input } from '@/components/ui/input'
import { valueUpdater } from '@/lib/utils'
import { computed } from 'vue'

import { ref } from 'vue'

import DataTablePagination from './DataTablePagination.vue'
import DataTableViewOptions from './DataTableViewOptions.vue'

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[]
    data: TData[]
    searchableColumns?: string[]
}>()

const searchs = computed(() =>
    (props.searchableColumns ?? []).map((key) => ({
        key,
        value: computed({
        get: () => (table.getColumn(key)?.getFilterValue() as string) || '',
        set: (val: string) => {
            table.getColumn(key)?.setFilterValue(val)
        },
        }),
    }))
)


const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})

const table = useVueTable({
    get data() { return props.data },
    get columns() { return props.columns },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
        get columnVisibility() { return columnVisibility.value },
        get rowSelection() { return rowSelection.value },
    },
})
</script>

<template>
    <div class="flex items-center p-5">
        <template v-for="search in searchs" :key="search.key">
            <Input class="max-w-sm" :placeholder="`Search by ${search.key}`" v-model="search.value" />
        </template>

        <DataTableViewOptions :table="table" />
    </div>
    <div class="border rounded-md p-2">
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
                    <TableRow v-for="row in table.getRowModel().rows" :key="row.id" :data-state="row.getIsSelected() ? 'selected' : undefined">
                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </TableCell>
                    </TableRow>
                </template>
                <template v-else>
                    <TableRow>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </div>
    <div class="py-2 px-5">
        <DataTablePagination :table="table" />
    </div>
</template>
