<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type ExpenseCategory } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle, List } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Expense Category Edit',
        href: route('expense-categories.index'),
    },
];

const props = defineProps<{
    expenseCategory: ExpenseCategory,
}>();

const form = useForm({
    id: props.expenseCategory.id,
    name: props.expenseCategory.name,
});

const submit = () => {
    form.put(route('expense-categories.update', props.expenseCategory.id), {
        onSuccess: () => {
            form.reset();
            toast.success('Expense category updated successfully', {
                description: new Date().toLocaleString(),
            });
        },
    });
};

</script>

<template>
    <Head title="Expense Category Edit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('expense-categories.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <List />
                </Link>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="name">Expense Category Name</Label>
                            <Input id="name" type="text" v-model="form.name" placeholder="Expense Category Name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Update Expense Category
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
