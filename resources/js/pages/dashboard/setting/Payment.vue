<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'vue-sonner'


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payment Setting',
        href: '/setting/payment',
    },
];

const props = defineProps<{
    paymentSetting: {
        payment_gateway?: string;
        payment_api_key?: string;
        payment_api_secret?: string;
    };
}>();

const form = useForm({
    payment_gateway: props.paymentSetting?.payment_gateway || '',
    payment_api_key: props.paymentSetting?.payment_api_key || '',
    payment_api_secret: props.paymentSetting?.payment_api_secret || '',
});

const submit = () => {
    form.post(route('payment.setting.update'), {
        onSuccess: () => {
            toast.success('Payment settings updated successfully', {
                description: new Date().toLocaleString()
            })
        },
    });
};
</script>

<template>
    <Head title="Payment Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <h1 class="text-2xl font-bold">Payment Settings</h1>
                    <div class="grid gap-6">
                        <div class="grid w-full max-w-sm items-center gap-1.5">
                            <Label for="payment_gateway" class="text-sm font-medium">Payment Gateway</Label>
                            <Select id="payment_gateway" v-model="form.payment_gateway" class="w-full">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select Payment Gateway" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Payment Gateways</SelectLabel>
                                        <SelectItem value="stripe">Stripe</SelectItem>
                                        <SelectItem value="paypal">PayPal</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.payment_gateway" class="mt-2" />
                        </div>
                        <div class="grid w-full max-w-sm items-center gap-1.5">
                            <Label for="payment_api_key" class="text-sm font-medium">API Key</Label>
                            <Input id="payment_api_key" type="text" v-model="form.payment_api_key" placeholder="API Key" />
                            <InputError :message="form.errors.payment_api_key" class="mt-2" />
                        </div>
                        <div class="grid w-full max-w-sm items-center gap-1.5">
                            <Label for="payment_api_secret" class="text-sm font-medium">API Secret</Label>
                            <Input id="payment_api_secret" type="text" v-model="form.payment_api_secret" placeholder="API Secret" />
                            <InputError :message="form.errors.payment_api_secret" class="mt-2" />
                        </div>
                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Update Settings
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
