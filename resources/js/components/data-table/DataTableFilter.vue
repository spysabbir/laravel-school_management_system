<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'

const props = defineProps<{
    columnKey: string
    table: any
    options: string[]
}>()

const column = computed(() => props.table.getColumn(props.columnKey))

const selectedValues = ref<string[]>([])

watchEffect(() => {
    const filterVal = column.value?.getFilterValue()
    selectedValues.value = Array.isArray(filterVal) ? filterVal : []
})

function toggleValue(value: string) {
    const current = new Set(selectedValues.value)
    if (current.has(value)) {
        current.delete(value)
    } else {
        current.add(value)
    }

    const updated = Array.from(current)
    selectedValues.value = updated
    column.value?.setFilterValue(updated)
}

function isChecked(value: string) {
    return selectedValues.value.includes(value)
}

function clearFilter() {
    selectedValues.value = []
    column.value?.setFilterValue([])
}
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" size="sm">Filter {{ columnKey }}</Button>
        </PopoverTrigger>
        <PopoverContent class="w-56">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium capitalize">{{ columnKey }}</p>
                <button class="text-xs text-muted-foreground hover:underline" @click="clearFilter">Clear</button>
            </div>
            <div class="grid gap-2 max-h-60 overflow-auto">
                <label v-for="item in props.options" :key="item" class="flex items-center gap-2 cursor-pointer">
                    <Checkbox :checked="isChecked(item)" @update:checked="() => toggleValue(item)" />
                    <span class="text-sm">{{ item }}</span>
                </label>
            </div>
        </PopoverContent>
    </Popover>
</template>
