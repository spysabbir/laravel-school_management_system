import { usePage } from "@inertiajs/vue3";

export function can(permission: string): boolean {
    const page = usePage();
    const userPermissions = page.props?.auth?.permissions || [];
    if (page.props?.auth?.roles?.includes('Super Admin')) {
        return true;
    }
    return userPermissions.includes(permission);
}

export function canAny(permissions: string[]): boolean {
    const page = usePage();
    const userPermissions = page.props?.auth?.permissions || [];
    if (page.props?.auth?.roles?.includes('Super Admin')) {
        return true;
    }
    return permissions.some(permission => userPermissions.includes(permission));
}
