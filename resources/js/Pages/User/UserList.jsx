import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function UserList({ auth, users }) {
    console.log(users)
    const { data, setData, post, processing, errors, reset } = useForm({
        user_id: '',
        role_id: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('users.add-user-role'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-ornge-200 leading-tight">Gestion des utilisateurs</h2>}
            dashboard="dashboard"
        >
            <Head title="Liste des utilisateurs" />

        
        
        <div className='p-5'>
            <Link  href={route('users.create')} className="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Ajouter un utilisateur</Link>
        </div>
        <div className='p-5'>
            <div className="grid grid-cols-3 grid-rows-5 gap-4">
                <div className="col-span-2">
                    <div className="shadow-md sm:rounded-lg">
                        <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" className="px-6 py-3">
                                        ID
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Nom
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Email
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Guichet
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Rôle
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {
                                    users.map((user, index) => 
                                    <tr key={index}>
                                        <td className="px-6 py-4">{user.id}</td>
                                        <td className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{user.name}</td>
                                        <td className="px-6 py-4">{user.email}</td>
                                        <td className="px-6 py-4">{user.guichet}</td>
                                        <td className="px-6 py-4">{user?.roles?.map(role => <li key={role.id}>{role.name}</li>)}</td>
                                        <td className="px-6 py-4">
                                            <Link href={route('users.show', [user.id])} className="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</Link>
                                        </td>
                                    </tr>
                                    )
                                }
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div className="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">

                        <form  onSubmit={submit}>
                            <div className="mb-6">
                                <label htmlFor="user_id" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Choisir l'utilisateur</label>
                                <select id="user_id" onChange={(e) => setData('user_id', e.target.value)} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                    <option></option>
                                    { users.map(user => <option key={user.id} value={user.id}>{user.name}</option>) }
                                </select>
                            </div>
                            <div className="mb-6">
                                <label htmlFor="role" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role</label>
                                <select id="role" onChange={(e) => setData('role_id', e.target.value)} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                    <option></option>
                                    <option value="1">Admin</option>
                                    <option value="2">Caller</option>
                                </select>
                            </div>
                            <button type="submit" className="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Valider</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

            
        </AuthenticatedLayout>
    );
}
