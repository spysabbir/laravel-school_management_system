<script setup lang="ts" generic="TData">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Checkbox } from '@/components/ui/checkbox'
import { Table } from '@tanstack/vue-table'

const props = defineProps<{
  column: ReturnType<Table<TData>['getColumn']>
  table: Table<TData>
}>()

const options = computed(() => {
  // Collect unique values and count them
  const values: Record<string, number> = {}
  props.table.getPreFilteredRowModel().rows.forEach((row) => {
    const value = row.getValue(props.column.id)
    if (value != null) {
      values[value] = (values[value] ?? 0) + 1
    }
  })
  return Object.entries(values)
})

const selectedValues = computed({
  get() {
    return (props.column.getFilterValue() as string[]) ?? []
  },
  set(val: string[]) {
    props.column.setFilterValue(val.length ? val : undefined)
  },
})

function toggleValue(value: string) {
  if (selectedValues.value.includes(value)) {
    selectedValues.value = selectedValues.value.filter((v) => v !== value)
  } else {
    selectedValues.value = [...selectedValues.value, value]
  }
}
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button variant="outline" size="sm" class="ml-auto">
        Status
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-48 p-2 space-y-2">
      <div v-for="[value, count] in options" :key="value" class="flex items-center gap-2">
        <Checkbox
          :checked="selectedValues.includes(value)"
          @update:checked="toggleValue(value)"
        />
        <span class="text-sm">{{ value }} ({{ count }})</span>
      </div>
    </PopoverContent>
  </Popover>
</template>
