import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function UserShow({ auth, user }) {
    const { data, setData, patch, processing, errors, reset } = useForm({
        name: user.name,
        email: user.email,
        guichet: user.guichet ?? 0,
        user_id: user.id,
        role_id: '',
    });

    const submit = (e) => {
        e.preventDefault();

        patch(route('users.update', [user.id]));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-ornge-200 leading-tight">Informations utilisateur</h2>}
            dashboard="dashboard"
        >
            <Head title="Modifier l'utilisateur" />

        

        <div className="py-12">
            
            <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                    <section className='max-w-xl'>
                        <header>
                            <h2 className="text-lg font-medium text-gray-900">Informations profil</h2>

                            <p className="mt-1 text-sm text-gray-600">
                                Mettez à jour les informations de profil.
                            </p>
                        </header>

                        <form  onSubmit={submit} className="mt-6 space-y-6">
                            <div className="mb-6">
                                <label htmlFor="name" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nom</label>
                                <input type='name' id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            </div>
                            <div className="mb-6">
                                <label htmlFor="email" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                <input type='email' value={data.email} onChange={(e) => setData('email', e.target.value)} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            </div>
                            <div className="mb-6">
                                <label htmlFor="guichet" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Guichet</label>
                                <input type='number' value={data.guichet} onChange={(e) => setData('guichet', e.target.value)} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            </div>
                            <button type="submit" className="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Valider</button>
                        </form>
                        
                    </section>
                </div>

                <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                    <section className='max-w-xl'>
                        <header>
                            <h2 className="text-lg font-medium text-gray-900">Rôle</h2>

                            <p className="mt-1 text-sm text-gray-600">
                                Mettez à jour le rôle.
                            </p>
                        </header>

                        <form  onSubmit={submit}>
                            <div className="mb-6">
                                <label htmlFor="role" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white"></label>
                                <select id="role" onChange={(e) => setData('role_id', e.target.value)} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                    <option></option>
                                    <option value="1">Admin</option>
                                    <option value="2">Caller</option>
                                </select>
                            </div>
                            <button type="submit" className="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Valider</button>
                        </form>
                        
                    </section>
                </div>
            </div>

        </div>

            
        </AuthenticatedLayout>
    );
}
