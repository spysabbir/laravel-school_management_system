<script setup lang="ts">
import { ref, h } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Group, type Classe, type Subject } from '@/types';
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
import { computed, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subject',
        href: '/subjects',
    },
];

const props = defineProps<{
    subjects: Subject[];
    classes: Classe[];
    groups: Group[];
}>();

const localSubjects = ref(props.subjects.map(subject => ({
    ...subject,
    class_name: subject.class.name,
    group_name: subject.group ? subject.group.name : 'N/A',
})));

const deletingIds = ref<number[]>([]);

const confirmDelete = (subjectId: number) => {
    if (confirm('Are you sure you want to delete this subject?')) {
        deletingIds.value.push(subjectId);

        router.delete(route('subjects.destroy', subjectId), {
            preserveScroll: true,
            onSuccess: () => {
                localSubjects.value = localSubjects.value.filter(
                    (item) => item.id !== subjectId
                );
                toast.success('Subject deleted successfully', {
                    description: new Date().toLocaleString(),
                });
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error deleting subject', {
                        description: errors.error,
                    });
                }
            },
            onFinish: () => {
                deletingIds.value = deletingIds.value.filter(id => id !== subjectId);
            },
        });
    }
};

const columns: ColumnDef<Subject>[] = [
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
            const className = row.original.class_name;
            return h('span', { class: 'text-sm text-gray-600 dark:text-gray-400' }, className);
        },
    },
    {
        id: 'group_id',
        accessorKey: 'group_id',
        header: ({ column }) => h(DataTableHeader, { column, title: 'Group' }),
        cell: ({ row }) => {
            const groupName = row.original.group_name;
            return h('span', { class: 'text-sm text-gray-600 dark:text-gray-400' }, groupName);
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
            const subject = row.original;
            const isDeleting = deletingIds.value.includes(subject.id);

            return h('div', { class: 'flex items-center gap-2' }, [
                h(Button, {
                    variant: 'outline',
                    onClick: () => openEditDialog(subject),
                }, [
                    h(Edit, { class: 'h-4 w-4' })
                ]),
                h(Button, {
                    variant: 'destructive',
                    disabled: isDeleting,
                    onClick: () => confirmDelete(subject.id),
                }, [
                    isDeleting ? h('span', { class: 'text-sm' }, '...') : h(Trash, { class: 'h-5 w-5' }),
                ])
            ]);
        }
    },
];

const form = useForm({
    class_id: null as number | null,
    group_id: null as number | null,
    type: '',
    name: '',
    code: '',
});

const filteredGroups = computed(() => {
    if (!form.class_id) return [];
    return props.groups.filter(group => group.class_id === form.class_id);
});

watch(() => form.class_id, (newClassId) => {
    const isValidGroup = props.groups.some(
        (group) => group.id === form.group_id && group.class_id === newClassId
    );

    if (!isValidGroup) {
        form.group_id = null;
    }
});

const editingSubject = ref<Subject | null>(null);
const dialogOpen = ref(false);

const openCreateDialog = () => {
    form.reset();
    form.clearErrors();
    editingSubject.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (subject: Subject) => {
    form.class_id = subject.class_id;

    // Check if the group belongs to selected class
    const validGroup = props.groups.find(
        group => group.id === subject.group_id && group.class_id === subject.class_id
    );

    form.group_id = validGroup ? subject.group_id : null;
    form.type = subject.type;
    form.name = subject.name;
    form.code = subject.code;
    editingSubject.value = subject;
    dialogOpen.value = true;
};

const submit = () => {
    if (editingSubject.value) {
        form.put(route('subjects.update', editingSubject.value.id), {
            onSuccess: () => {
                toast.success('Subject updated successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localSubjects.value = props.subjects.map(subject => ({
                    ...subject,
                    class_name: subject.class.name,
                    group_name: subject.group ? subject.group.name : 'N/A',
                }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error updating subject', {
                        description: errors.error,
                    });
                }
            },
        });
    } else {
        form.post(route('subjects.store'), {
            onSuccess: () => {
                toast.success('Subject created successfully', {
                    description: new Date().toLocaleString(),
                });
                form.reset();
                form.clearErrors();
                dialogOpen.value = false;

                localSubjects.value = props.subjects.map(subject => ({
                    ...subject,
                    class_name: subject.class.name,
                    group_name: subject.group ? subject.group.name : 'N/A',
                }));
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error('Error creating subject', {
                        description: errors.error,
                    });
                }
            },
        });
    }
};
</script>

<template>
    <Head title="Subject" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button @click="openCreateDialog" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" /> New Subject
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{{ editingSubject ? 'Edit' : 'Create' }} Subject</DialogTitle>
                            <DialogDescription>
                                {{ editingSubject ? 'Edit' : 'Create' }} an subject to manage your subjects effectively.
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
                                    <Label for="group_id">Group</Label>
                                    <Select
                                        id="group_id"
                                        v-model="form.group_id"
                                        :disabled="!form.class_id || filteredGroups.length === 0"
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                :placeholder="!form.class_id ? 'Select class first' : (filteredGroups.length === 0 ? 'No groups available' : 'Select a group')"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup v-if="filteredGroups.length">
                                                <SelectLabel>Groups</SelectLabel>
                                                <SelectItem
                                                    v-for="group in filteredGroups"
                                                    :key="group.id"
                                                    :value="group.id"
                                                >
                                                    {{ group.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.group_id" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="type">Type</Label>
                                    <Select id="type" v-model="form.type">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select a type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectLabel>Types</SelectLabel>
                                                <SelectItem value="Compulsory">Compulsory</SelectItem>
                                                <SelectItem value="Optional">Optional</SelectItem>
                                                <SelectItem value="Practical">Practical</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.type" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="name">Subject Name</Label>
                                    <Input id="name" type="text" v-model="form.name" placeholder="Subject Name" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="code">Subject Code</Label>
                                    <Input id="code" type="text" v-model="form.code" placeholder="Subject Code" />
                                    <InputError :message="form.errors.code" />
                                </div>
                                <div class="flex justify-end">
                                    <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        {{ editingSubject ? 'Update' : 'Create' }}
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
                <DataTable :columns="columns" :data="localSubjects" :searchable-columns="['name']" :filterable-columns="['class_name', 'status']" />
            </div>
        </div>
    </AppLayout>
</template>
