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
        title: 'Sms Setting',
        href: '/setting/sms',
    },
];

const props = defineProps<{
    smsSetting: {
        sms_driver?: string;
        sms_api_key?: string;
        sms_api_secret?: string;
        sms_from_number?: string;
    };
}>();

const form = useForm({
    sms_driver: props.smsSetting?.sms_driver || '',
    sms_api_key: props.smsSetting?.sms_api_key || '',
    sms_api_secret: props.smsSetting?.sms_api_secret || '',
    sms_from_number: props.smsSetting?.sms_from_number || '',
});

const submit = () => {
    form.post(route('sms.setting.update'), {
        onSuccess: () => {
            toast.success('Sms settings updated successfully', {
                description: new Date().toLocaleString()
            })
        },
    });
};
</script>

<template>
    <Head title="Sms Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <h1 class="text-2xl font-bold">Sms Settings</h1>
                    <div class="grid gap-6">
                        <div class="grid w-full max-w-sm items-center gap-1.5">
                            <Label for="sms_driver" class="text-sm font-medium">Sms Driver</Label>
                            <Select id="sms_driver" v-model="form.sms_driver" class="w-full">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select Sms Driver" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Available Drivers</SelectLabel>
                                        <SelectItem value="twilio">Twilio</SelectItem>
                                        <SelectItem value="nexmo">Nexmo</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.sms_driver" class="mt-2" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sms_api_key">Sms API Key</Label>
                            <Input id="sms_api_key" type="text" v-model="form.sms_api_key" placeholder="Sms API Key" />
                            <InputError :message="form.errors.sms_api_key" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sms_api_secret">Sms API Secret</Label>
                            <Input id="sms_api_secret" type="text" v-model="form.sms_api_secret" placeholder="Sms API Secret" />
                            <InputError :message="form.errors.sms_api_secret" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sms_from_number">Sms From Number</Label>
                            <Input id="sms_from_number" type="text" v-model="form.sms_from_number" placeholder="Sms From Number" />
                            <InputError :message="form.errors.sms_from_number" />
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
