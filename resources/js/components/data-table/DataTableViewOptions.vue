<script setup lang="ts">
import type { Table } from '@tanstack/vue-table'
import { computed } from 'vue'
import { Eye } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger, DropdownMenuItem } from '@/components/ui/dropdown-menu'

interface DataTableViewOptionsProps {
    table: Table<any>
}

const props = defineProps<DataTableViewOptionsProps>()

const columns = computed(() =>
    props.table.getAllColumns().filter(
        column => typeof column.accessorFn !== 'undefined' && column.getCanHide(),
    )
)

const showAll = () => {
    columns.value.forEach(column => column.toggleVisibility(true))
}

const hideAll = () => {
    columns.value.forEach(column => column.toggleVisibility(false))
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm" class="hidden h-8 ml-auto lg:flex">
                <Eye class="w-4 h-4 mr-2" />
                View
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-[180px]">
            <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
            <DropdownMenuSeparator />

            <div class="flex items-center justify-between px-2 py-1">
                <DropdownMenuItem @click="showAll" class="cursor-pointer rounded-md bg-gray-700 hover:bg-gray-100">
                    Show all
                </DropdownMenuItem>
                <DropdownMenuItem @click="hideAll" class="cursor-pointer rounded-md bg-gray-700 hover:bg-gray-100">
                    Hide all
                </DropdownMenuItem>
            </div>

            <DropdownMenuSeparator />

            <DropdownMenuCheckboxItem v-for="column in columns" :key="column.id" class="capitalize" :modelValue="column.getIsVisible()" @update:modelValue="(value) => column.toggleVisibility(!!value)">
                {{ column.id }}
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
