<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import type { Table } from '@tanstack/vue-table'
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

interface DataTablePaginationProps {
    table: Table<any>
    storageKey?: string
}

const props = defineProps<DataTablePaginationProps>()

const STORAGE_KEY_PAGE_SIZE = (props.storageKey ?? 'table') + '-page-size'
const STORAGE_KEY_PAGE_INDEX = (props.storageKey ?? 'table') + '-page-index'

const pageSizes = [10, 25, 50, 100, 'All'] as const
const selectedPageSize = ref<string>('10')
const goToPageNumber = ref<number>(1)

const getTotalRows = () => props.table.getCoreRowModel().rows.length

function applyPageSize(value: string) {
    if (value === 'All') {
        props.table.setPageSize(getTotalRows())
    } else {
        props.table.setPageSize(Number(value))
    }
}

onMounted(() => {
    const savedSize = localStorage.getItem(STORAGE_KEY_PAGE_SIZE)
    if (savedSize && (pageSizes as readonly string[]).includes(savedSize)) {
        selectedPageSize.value = savedSize
        applyPageSize(savedSize)
    } else {
        selectedPageSize.value = '10'
        props.table.setPageSize(10)
    }

    const savedPageIndex = localStorage.getItem(STORAGE_KEY_PAGE_INDEX)
    if (savedPageIndex && !isNaN(Number(savedPageIndex))) {
        const index = Number(savedPageIndex)
        if (index >= 0 && index < props.table.getPageCount()) {
        props.table.setPageIndex(index)
        goToPageNumber.value = index + 1
        }
    }
})

watch(
    () => props.table.getState().pagination.pageSize,
    (newPageSize) => {
        const totalRows = getTotalRows()
        const newValue = newPageSize >= totalRows ? 'All' : String(newPageSize)
        if (selectedPageSize.value !== newValue) {
        selectedPageSize.value = newValue
        localStorage.setItem(STORAGE_KEY_PAGE_SIZE, newValue)
        }
    }
)

watch(
    () => props.table.getState().pagination.pageIndex,
    (newIndex) => {
        goToPageNumber.value = newIndex + 1
        localStorage.setItem(STORAGE_KEY_PAGE_INDEX, String(newIndex))
    }
)

function handlePageSizeChange(value: string) {
    selectedPageSize.value = value
    localStorage.setItem(STORAGE_KEY_PAGE_SIZE, value)
    applyPageSize(value)
}

function handleGoToPage() {
    const pageIndex = goToPageNumber.value - 1
    if (pageIndex >= 0 && pageIndex < props.table.getPageCount()) {
        props.table.setPageIndex(pageIndex)
    } else {
        goToPageNumber.value = props.table.getState().pagination.pageIndex + 1
    }
}
</script>


<template>
    <div class="flex items-center justify-between px-2">
        <div class="flex-1 text-sm text-muted-foreground">
            {{ table.getFilteredSelectedRowModel().rows.length }} of
            {{ table.getFilteredRowModel().rows.length }} row(s) selected.
        </div>
        <div class="flex items-center space-x-6 lg:space-x-8">
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium">Rows per page</p>
                <Select :model-value="selectedPageSize" @update:model-value="handlePageSizeChange">
                <SelectTrigger class="h-8 w-[80px]">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent side="top">
                    <SelectItem v-for="option in pageSizes" :key="option" :value="`${option}`">
                    {{ option }}
                    </SelectItem>
                </SelectContent>
                </Select>
            </div>

            <div class="flex w-[100px] items-center justify-center text-sm font-medium">
                Page {{ table.getState().pagination.pageIndex + 1 }} of {{ table.getPageCount() }}
            </div>

            <div class="flex items-center gap-1">
                Go to page:
                <Input
                type="number"
                v-model.number="goToPageNumber"
                @change="handleGoToPage"
                class="border p-1 rounded w-16"
                />
            </div>

            <div class="flex items-center space-x-2">
                <Button variant="outline" class="hidden w-8 h-8 p-0 lg:flex" :disabled="!table.getCanPreviousPage()" @click="table.setPageIndex(0)">
                    <span class="sr-only">Go to first page</span>
                    <ChevronsLeft class="w-4 h-4" />
                </Button>
                <Button variant="outline" class="w-8 h-8 p-0" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                    <span class="sr-only">Go to previous page</span>
                    <ChevronLeft class="w-4 h-4" />
                </Button>
                <Button variant="outline" class="w-8 h-8 p-0" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    <span class="sr-only">Go to next page</span>
                    <ChevronRight class="w-4 h-4" />
                </Button>
                <Button variant="outline" class="hidden w-8 h-8 p-0 lg:flex" :disabled="!table.getCanNextPage()" @click="table.setPageIndex(table.getPageCount() - 1)">
                    <span class="sr-only">Go to last page</span>
                    <ChevronsRight class="w-4 h-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
