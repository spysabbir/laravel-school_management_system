<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Group } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Plus, Trash, Edit } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Group',
        href: '/groups',
    },
];

defineProps<{
    groups: Group[],
}>();

const confirmDelete = (groupId: number) => {
    if (confirm('Are you sure you want to delete this group?')) {
        router.delete(route('groups.destroy', groupId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
                toast.success('Group deleted successfully', {
                    description: new Date().toLocaleString(),
                });
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
                <Link :href="route('groups.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <Plus class="h-5 w-5" />
                </Link>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Table>
                    <TableCaption>A list of your groups</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Class</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(group, index) in groups" :key="index">
                            <TableCell>{{ group.id }}</TableCell>
                            <TableCell>{{ group.class.name }}</TableCell>
                            <TableCell>{{ group.name }}</TableCell>
                            <TableCell>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ new Date(group.created_at).toLocaleString('en-US', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',hour12:true}) }}
                                </span>
                            </TableCell>
                            <TableCell class="flex items-center gap-2">
                                <Link :href="route('groups.edit', group.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                                    <Edit class="h-5 w-5" />
                                </Link>
                                <button @click="confirmDelete(group.id)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">
                                    <Trash class="h-5 w-5" />
                                </button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
