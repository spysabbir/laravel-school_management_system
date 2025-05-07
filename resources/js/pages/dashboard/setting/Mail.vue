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
        title: 'Mail Setting',
        href: '/setting/mail',
    },
];

const props = defineProps<{
    mailSetting: {
        mail_driver?: string;
        mail_host?: string;
        mail_port?: string;
        mail_username?: string;
        mail_password?: string;
        mail_encryption?: string;
        mail_from_address?: string;
    };
}>();

const form = useForm({
    mail_driver: props.mailSetting?.mail_driver || '',
    mail_host: props.mailSetting?.mail_host || '',
    mail_port: props.mailSetting?.mail_port || '',
    mail_username: props.mailSetting?.mail_username || '',
    mail_password: props.mailSetting?.mail_password || '',
    mail_encryption: props.mailSetting?.mail_encryption || '',
    mail_from_address: props.mailSetting?.mail_from_address || '',
});

const submit = () => {
    form.post(route('mail.setting.update'), {
        onSuccess: () => {
            toast.success('Mail settings updated successfully', {
                description: new Date().toLocaleString()
            })
        },
    });
};
</script>

<template>
    <Head title="Mail Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <form @submit.prevent="submit" class="flex h-full flex-col items-center justify-center gap-4 p-4">
                    <h1 class="text-2xl font-bold">Mail Settings</h1>
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="mail_driver">Mail Driver</Label>
                            <Input id="mail_driver" type="text" v-model="form.mail_driver" placeholder="Mail Driver" />
                            <InputError :message="form.errors.mail_driver" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="mail_host">Mail Host</Label>
                            <Input id="mail_host" type="text" v-model="form.mail_host" placeholder="Mail Host" />
                            <InputError :message="form.errors.mail_host" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="mail_port">Mail Port</Label>
                            <Input id="mail_port" type="text" v-model="form.mail_port" placeholder="Mail Port" />
                            <InputError :message="form.errors.mail_port" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="mail_username">Mail Username</Label>
                            <Input id="mail_username" type="text" v-model="form.mail_username" placeholder="Mail Username" />
                            <InputError :message="form.errors.mail_username" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="mail_password">Mail Password</Label>
                            <Input id="mail_password" type="password" v-model="form.mail_password" placeholder="Mail Password" />
                            <InputError :message="form.errors.mail_password" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="mail_encryption">Mail Encryption</Label>
                            <Select id="mail_encryption" v-model="form.mail_encryption">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Mail Encryption" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Mail Encryption</SelectLabel>
                                        <SelectItem value="tls">TLS</SelectItem>
                                        <SelectItem value="ssl">SSL</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.mail_encryption" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="mail_from_address">Mail From Address</Label>
                            <Input id="mail_from_address" type="text" v-model="form.mail_from_address" placeholder="Mail From Address" />
                            <InputError :message="form.errors.mail_from_address" />
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
