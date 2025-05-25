<script setup lang="ts">
import { ref, h } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type ExpenseCategory } from '@/types';
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
        header: ({ column }) =>
        h(DataTableHeader, {
            column,
            title: 'ID',
        }),
        cell: ({ row }) => row.original.id,
    },
    {
        id: 'name',
        accessorKey: 'name',
        header: ({ column }) =>
        h(DataTableHeader, {
            column,
            title: 'Name',
        }),
        cell: ({ row }) => row.original.name,
    },
    {
        id: 'status',
        accessorKey: 'status',
        header: 'Status',
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
            const expenseCategory = row.original;
            const isDeleting = deletingIds.value.includes(expenseCategory.id);

            return h('div', { class: 'flex items-center gap-2' }, [
                h(Button, {
                    variant: 'outline',
                    onClick: () => openEditDialog(expenseCategory),
                }, [
                    h(Edit, { class: 'h-4 w-4' })
                ]),
                h(Button, {
                    variant: 'destructive',
                    disabled: isDeleting,
                    onClick: () => confirmDelete(expenseCategory.id),
                }, [
                    isDeleting ? h('span', { class: 'text-sm' }, '...') : h(Trash, { class: 'h-5 w-5' }),
                ])
            ]);
        }
    },
];

const form = useForm({
    name: '',
});

const editingCategory = ref<ExpenseCategory | null>(null);
const dialogOpen = ref(false);

const openCreateDialog = () => {
    form.reset();
    form.clearErrors();
    editingCategory.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (category: ExpenseCategory) => {
    form.name = category.name;
    editingCategory.value = category;
    dialogOpen.value = true;
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('expense-categories.update', editingCategory.value.id), {
            onSuccess: () => {
                toast.success('Expense category updated successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localExpenseCategories.value = [...props.expenseCategories];
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error updating expense category', {
                        description: errors.error,
                    });
                }
            },
        });
    } else {
        form.post(route('expense-categories.store'), {
            onSuccess: () => {
                toast.success('Expense category created successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localExpenseCategories.value = [...props.expenseCategories];
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error creating expense category', {
                        description: errors.error,
                    });
                }
            },
        });
    }
};
</script>

<template>
    <Head title="Expense Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button @click="openCreateDialog" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" /> New Category
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{{ editingCategory ? 'Edit' : 'Create' }} Expense Category</DialogTitle>
                            <DialogDescription>
                                {{ editingCategory ? 'Edit' : 'Create' }} an expense category to manage your expenses effectively.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="submit" class="p-4">
                            <div class="grid gap-6">
                                <div class="grid gap-2">
                                    <Label for="name">Expense Category Name</Label>
                                    <Input id="name" type="text" v-model="form.name" placeholder="Expense Category Name" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="flex justify-end">
                                    <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        {{ editingCategory ? 'Update' : 'Create' }}
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
                <DataTable :columns="columns" :data="localExpenseCategories" :searchable-columns="['name']" :filterable-columns="['status']" />
            </div>
        </div>
    </AppLayout>
</template>
