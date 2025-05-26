<script setup lang="ts">
import { ref, h } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Shift, type Classe } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Trash, Edit } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ColumnDef } from '@tanstack/vue-table';
import DataTableHeader from '@/components/data-table/DataTableHeader.vue';
import { Button } from '@/components/ui/button';
import DataTable from '../../../components/data-table/DataTable.vue';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Shift',
        href: '/shifts',
    },
];

const props = defineProps<{
    shifts: Shift[];
    classes: Classe[];
}>();

const localShifts = ref(props.shifts.map(shift => ({
    ...shift,
    class_name: shift.class.name,
})));

const deletingIds = ref<number[]>([]);

const confirmDelete = (shiftId: number) => {
    if (confirm('Are you sure you want to delete this shift?')) {
        deletingIds.value.push(shiftId);

        router.delete(route('shifts.destroy', shiftId), {
            preserveScroll: true,
            onSuccess: () => {
                localShifts.value = localShifts.value.filter(
                    (item) => item.id !== shiftId
                );
                toast.success('Shift deleted successfully', {
                    description: new Date().toLocaleString(),
                });
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error deleting shift', {
                        description: errors.error,
                    });
                }
            },
            onFinish: () => {
                deletingIds.value = deletingIds.value.filter(id => id !== shiftId);
            },
        });
    }
};

const columns: ColumnDef<Shift>[] = [
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
        id: 'class_id',
        accessorKey: 'class_id',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Class' }),
        cell: ({ row }) => {
            const classId = row.original.class_name;
            return classId
        },
    },
    {
        id: 'name',
        accessorKey: 'name',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Name' }),
        cell: ({ row }) => row.original.name,
    },
    {
        id: 'status',
        accessorKey: 'status',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Status' }),
        cell: ({ row }) => {
            const status = row.original.status;
            return h('span', {
                class: `inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`,
            }, status);
        },
    },
    {
        id: 'actions',
        accessorKey: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const shift = row.original;
            const isDeleting = deletingIds.value.includes(shift.id);

            return h('div', { class: 'flex items-center gap-2' }, [
                h(Button, {
                    variant: 'outline',
                    onClick: () => openEditDialog(shift),
                }, [
                    h(Edit, { class: 'h-4 w-4' })
                ]),
                h(Button, {
                    variant: 'destructive',
                    disabled: isDeleting,
                    onClick: () => confirmDelete(shift.id),
                }, [
                    isDeleting ? h('span', { class: 'text-sm' }, '...') : h(Trash, { class: 'h-5 w-5' }),
                ])
            ]);
        }
    },
];

const form = useForm({
    class_id: null as number | null,
    name: '',
});

const editingShift = ref<Shift | null>(null);
const dialogOpen = ref(false);

const openCreateDialog = () => {
    form.reset();
    form.clearErrors();
    editingShift.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (shift: Shift) => {
    form.class_id = shift.class_id;
    form.name = shift.name;
    editingShift.value = shift;
    dialogOpen.value = true;
};

const submit = () => {
    if (editingShift.value) {
        form.put(route('shifts.update', editingShift.value.id), {
            onSuccess: () => {
                toast.success('Shift updated successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localShifts.value = props.shifts.map(shift => ({
                    ...shift,
                    class_name: shift.class.name,
                }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error updating shift', {
                        description: errors.error,
                    });
                }
            },
        });
    } else {
        form.post(route('shifts.store'), {
            onSuccess: () => {
                toast.success('Shift created successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localShifts.value = props.shifts.map(shift => ({
                    ...shift,
                    class_name: shift.class.name,
                }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error creating shift', {
                        description: errors.error,
                    });
                }
            },
        });
    }
};
</script>

<template>
    <Head title="Shift" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button @click="openCreateDialog" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" /> New Shift
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{{ editingShift ? 'Edit' : 'Create' }} Shift</DialogTitle>
                            <DialogDescription>
                                {{ editingShift ? 'Edit' : 'Create' }} an shift to manage your shifts effectively.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="submit" class="p-4">
                            <div class="grid gap-6">
                                <div class="grid gap-2">
                                    <Label for="class_id">Class</Label>
                                    <Select id="class_id" v-model="form.class_id">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select a class" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel>Classes</SelectLabel>
                                                <SelectItem v-for="classe in classes" :key="classe.id" :value="classe.id">
                                                    {{ classe.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.class_id" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="name">Shift Name</Label>
                                    <Input id="name" type="text" v-model="form.name" placeholder="Shift Name" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="flex justify-end">
                                    <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        {{ editingShift ? 'Update' : 'Create' }}
                                    </Button>
                                </div>
                            </div>
                        </form>
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button type="button" variant="secondary">
                                    Close
                                </Button>
                            </DialogClose>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <DataTable :columns="columns" :data="localShifts" :searchable-columns="['name']" :filterable-columns="['class_name', 'status']" />
            </div>
        </div>
    </AppLayout>
</template>
