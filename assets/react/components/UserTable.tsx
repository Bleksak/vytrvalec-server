import React, { useEffect, useState } from 'react';
import { useTranslation } from "react-i18next";
import { User } from '../types';
import TableRow from './user/TableRow';
import { getAllUsers } from '../api/UserApi';

const UserTable = ({ userId }: { userId: number }) => {
    const [users, setUsers] = useState<User[]>([]);
    const [filter, setFilter] = useState<string>('');

    useEffect(() => {
        getAllUsers().then(setUsers)
    }, []);

    const search = (input: any) => {
        setFilter(input.target.value);
    }

    const [t, _] = useTranslation();

    const filteredUsers = users.filter((user: User) => (user.firstName + ' ' + user.lastName).toLowerCase().includes(filter.toLowerCase()));

    return (
        <>
            {/* @ts-ignore */}
            <input type="text" id="search-bar" onKeyUp={search} placeholder={t('search_name')} />
            <table className='table table-striped table-hover table-sm'>
                <thead>
                    <tr>
                        <th scope="col" className='text-center'>
                            {t('name')}
                        </th>
                        <th scope="col" className='text-center'>
                            {t('faculty')}
                        </th>
                        <th scope="col" className='text-center'>
                            {t('email')}
                        </th>
                        <th scope="col" className='text-center'>
                            {t('status')}
                        </th>
                        <th scope="col" className='text-center'>
                            {t('access_right')}
                        </th>
                        <th scope="col" className='text-center'>
                            {t('action')}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {filteredUsers.length > 0 && filteredUsers.map((user: User) =>
                        <TableRow key={user.id} user={user} userId={userId}></TableRow>
                    )}
                </tbody>
            </table>
        </>
    )
}

export default UserTable;

