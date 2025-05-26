<script setup lang="ts">
import { ref, h } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Group, type Classe } from '@/types';
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
        title: 'Group',
        href: '/groups',
    },
];

const props = defineProps<{
    groups: Group[];
    classes: Classe[];
}>();

const localGroups = ref(props.groups.map(group => ({
    ...group,
    class_name: group.class.name,
})));

const deletingIds = ref<number[]>([]);

const confirmDelete = (groupId: number) => {
    if (confirm('Are you sure you want to delete this group?')) {
        deletingIds.value.push(groupId);

        router.delete(route('groups.destroy', groupId), {
            preserveScroll: true,
            onSuccess: () => {
                localGroups.value = localGroups.value.filter(
                    (item) => item.id !== groupId
                );
                toast.success('Group deleted successfully', {
                    description: new Date().toLocaleString(),
                });
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error deleting group', {
                        description: errors.error,
                    });
                }
            },
            onFinish: () => {
                deletingIds.value = deletingIds.value.filter(id => id !== groupId);
            },
        });
    }
};

const columns: ColumnDef<Group>[] = [
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
            const group = row.original;
            const isDeleting = deletingIds.value.includes(group.id);

            return h('div', { class: 'flex items-center gap-2' }, [
                h(Button, {
                    variant: 'outline',
                    onClick: () => openEditDialog(group),
                }, [
                    h(Edit, { class: 'h-4 w-4' })
                ]),
                h(Button, {
                    variant: 'destructive',
                    disabled: isDeleting,
                    onClick: () => confirmDelete(group.id),
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

const editingGroup = ref<Group | null>(null);
const dialogOpen = ref(false);

const openCreateDialog = () => {
    form.reset();
    form.clearErrors();
    editingGroup.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (group: Group) => {
    form.class_id = group.class_id;
    form.name = group.name;
    editingGroup.value = group;
    dialogOpen.value = true;
};

const submit = () => {
    if (editingGroup.value) {
        form.put(route('groups.update', editingGroup.value.id), {
            onSuccess: () => {
                toast.success('Group updated successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localGroups.value = props.groups.map(group => ({
                    ...group,
                    class_name: group.class.name,
                }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error updating group', {
                        description: errors.error,
                    });
                }
            },
        });
    } else {
        form.post(route('groups.store'), {
            onSuccess: () => {
                toast.success('Group created successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localGroups.value = props.groups.map(group => ({
                    ...group,
                    class_name: group.class.name,
                }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error creating group', {
                        description: errors.error,
                    });
                }
            },
        });
    }
};
</script>

<template>
    <Head title="Group" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button @click="openCreateDialog" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" /> New Group
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{{ editingGroup ? 'Edit' : 'Create' }} Group</DialogTitle>
                            <DialogDescription>
                                {{ editingGroup ? 'Edit' : 'Create' }} an group to manage your groups effectively.
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
                                    <Label for="name">Group Name</Label>
                                    <Input id="name" type="text" v-model="form.name" placeholder="Group Name" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="flex justify-end">
                                    <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        {{ editingGroup ? 'Update' : 'Create' }}
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
                <DataTable :columns="columns" :data="localGroups" :searchable-columns="['name']" :filterable-columns="['class_name', 'status']" />
            </div>
        </div>
    </AppLayout>
</template>
