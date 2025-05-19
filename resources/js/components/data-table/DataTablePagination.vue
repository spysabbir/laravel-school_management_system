<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import type { Table } from '@tanstack/vue-table'
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'

interface DataTablePaginationProps {
  table: Table<any>
  storageKey?: string // optional: allow setting a custom key
}

const props = defineProps<DataTablePaginationProps>()

const STORAGE_KEY = props.storageKey ?? 'table-page-size'

const pageSizes = [10, 25, 50, 100, 'All'] as const
const selectedPageSize = ref<string>('10') // default

const getTotalRows = () => props.table.getCoreRowModel().rows.length

function applyPageSize(value: string) {
  if (value === 'All') {
    props.table.setPageSize(getTotalRows())
  } else {
    props.table.setPageSize(Number(value))
  }
}

// Load from localStorage on mount
onMounted(() => {
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved && (pageSizes as readonly string[]).includes(saved)) {
    selectedPageSize.value = saved
    applyPageSize(saved)
  } else {
    // fallback to default (10)
    selectedPageSize.value = '10'
    props.table.setPageSize(10)
  }
})

// Watch for page size changes from table state (external control)
watch(
  () => props.table.getState().pagination.pageSize,
  (newPageSize) => {
    const totalRows = getTotalRows()
    const newValue = newPageSize >= totalRows ? 'All' : String(newPageSize)
    if (selectedPageSize.value !== newValue) {
      selectedPageSize.value = newValue
      localStorage.setItem(STORAGE_KEY, newValue)
    }
  }
)

function handlePageSizeChange(value: string) {
  selectedPageSize.value = value
  localStorage.setItem(STORAGE_KEY, value)
  applyPageSize(value)
}
</script>

<template>
  <div class="flex items-center justify-between px-2">
    <div class="flex-1 text-sm text-muted-foreground">
      {{ table.getFilteredSelectedRowModel().rows.length }} of
      {{ table.getFilteredRowModel().rows.length }} row(s) selected.
    </div>
    <div class="flex items-center space-x-6 lg:space-x-8">
      <!-- Rows per page -->
      <div class="flex items-center space-x-2">
        <p class="text-sm font-medium">Rows per page</p>
        <Select :model-value="selectedPageSize" @update:model-value="handlePageSizeChange">
          <SelectTrigger class="h-8 w-[80px]">
            <SelectValue />
          </SelectTrigger>
          <SelectContent side="top">
            <SelectItem
              v-for="option in pageSizes"
              :key="option"
              :value="`${option}`"
            >
              {{ option }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Page info -->
      <div class="flex w-[100px] items-center justify-center text-sm font-medium">
        Page {{ table.getState().pagination.pageIndex + 1 }} of {{ table.getPageCount() }}
      </div>

      <!-- Page navigation buttons -->
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
