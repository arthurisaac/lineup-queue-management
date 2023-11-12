import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Tableau de bord</h2>}
            dashboard="dashboard"
        >
            <Head title="Dashboard" />

            

            
        </AuthenticatedLayout>
    );
}
