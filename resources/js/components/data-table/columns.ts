import { ColumnDef } from '@tanstack/vue-table'
import DropdownAction from '@/components/data-table/DropdownAction.vue'
import DataTableColumnHeader from '@/components/data-table/DataTableColumnHeader.vue'
import { h } from 'vue'
import { ArrowUpDown } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'

export const columns: ColumnDef<Payment>[] = [
    {
        id: 'select',
        header: ({ table }) => h(Checkbox, {
            'modelValue': table.getIsAllPageRowsSelected(),
            'onUpdate:modelValue': (value: boolean) => table.toggleAllPageRowsSelected(!!value),
            'ariaLabel': 'Select all',
        }),
        cell: ({ row }) => h(Checkbox, {
            'modelValue': row.getIsSelected(),
            'onUpdate:modelValue': (value: boolean) => row.toggleSelected(!!value),
            'ariaLabel': 'Select row',
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        id: 'id',
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) => {
        const payment = row.original

        return h('span', { class: 'text-gray-500' }, payment.id)
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
    },
    {
        id: 'amount',
        accessorKey: 'amount',
        header: ({ column }) => (
        h(DataTableColumnHeader, {
            column: column,
            title: 'Amount'
        })
    ),
        cell: ({ row }) => {
        const payment = row.original

        return h('span', { class: 'text-gray-500' }, payment.amount)
        },
    },
    {
        id: 'status',
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
        const payment = row.original

        return h('span', { class: 'text-gray-500' }, payment.status)
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
        const payment = row.original

        return h('div', { class: 'relative' }, h(DropdownAction, {
            payment,
        }))
        },
    },
]
