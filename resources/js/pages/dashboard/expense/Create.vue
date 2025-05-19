<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type ExpenseCategory } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { List, LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea'
import { toast } from 'vue-sonner';
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { ref } from 'vue'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { CalendarDate, DateFormatter, getLocalTimeZone, today, type DateValue } from '@internationalized/date'


const df = new DateFormatter('en-US', {
    dateStyle: 'long',
})

const dateValue = ref<DateValue>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Expense Create',
        href: route('expenses.index'),
    },
];

defineProps<{
    expenseCategories: ExpenseCategory[];
}>();

const form = useForm({
    expense_category_id: '',
    title: '',
    description: '',
    amount: '',
    date: '',
});

const submit = () => {
    form.post(route('expenses.store'), {
        onSuccess: () => {
            toast.success('Expense created successfully', {
                description: new Date().toLocaleString(),
            });
            form.reset();
            form.clearErrors();
        },
    });
};

</script>

<template>
    <Head title="Expense Create" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-4 flex items-center justify-end">
                <Link :href="route('expenses.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <List />
                </Link>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <h1 class="text-2xl font-bold">Create Expense</h1>
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label>Expense Category</Label>
                            <Select v-model="form.expense_category_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Expense Category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Expense Category</SelectLabel>
                                        <SelectItem v-for="expenseCategory in expenseCategories" :key="expenseCategory.id" :value="String(expenseCategory.id)">
                                            {{ expenseCategory.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.expense_category_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="title">Expense Title</Label>
                            <Input id="title" type="text" v-model="form.title" placeholder="Expense Title" />
                            <InputError :message="form.errors.title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Expense Description</Label>
                            <Textarea v-model="form.description" placeholder="Enter your expense description" />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="amount">Expense Amount</Label>
                            <Input id="amount" type="number" v-model="form.amount" placeholder="Expense Amount" />
                            <InputError :message="form.errors.amount" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="amount">Expense Date</Label>
                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button variant="outline" class="w-[280px] justify-start text-left font-normal text-muted-foreground">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ dateValue ? df.format(dateValue.toDate(getLocalTimeZone())) : "Pick a date" }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0">
                                <Calendar
                                v-model="dateValue"
                                @update:model-value="(value) => {
                                    form.date = value ? value.toString() : '';
                                }"
                                :value="dateValue"
                                :min-value="new CalendarDate(2025, 1, 1)"
                                :max-value="today(getLocalTimeZone())"
                                initial-focus />
                                </PopoverContent>
                            </Popover>
                            <InputError :message="form.errors.date" />
                        </div>

                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Create Expense
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
