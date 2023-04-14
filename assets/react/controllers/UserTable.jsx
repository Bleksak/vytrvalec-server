import React, { useEffect, useState } from 'react';
import { useTranslation } from "react-i18next";
import _ from '../i8n'

const users_route = '/api/management/users';
const user_profile_route = '/user/profile/';
const user_ban_route = '/api/management/ban';
const user_admin_route = '/api/management/admin';

export default function UserTable(userId) {
    const [users, setUsers] = useState([]);
    const [filter, setFilter] = useState('');

    useEffect( () => {
        const fetchData = async () => {
            const result = await fetch(users_route);
            const users = await result.json();

            setUsers(users);
        }

        fetchData();
    }, []);

    const search = (input) => {
        setFilter(input.target.value);
    }

    const { t, i18n } = useTranslation();

    const filteredUsers = users.filter((user) => (user.firstName + ' ' + user.lastName).toLowerCase().includes(filter.toLowerCase()));

    return <>
    <input type="text" id="search-bar" onKeyUp={search} placeholder={t('search_name')}/>
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
            { filteredUsers.length > 0 && filteredUsers.map( (user) => 
                <TableRow key={user.id} user={user} userId={userId} user_admin_route={user_admin_route} user_profile_route={user_profile_route} user_ban_route={user_ban_route}></TableRow>
             )}
        </tbody>
    </table>
    </>
}

function TableRow({user, userId, user_profile_route, user_admin_route, user_ban_route}) {
    const { t, i18n } = useTranslation()

    const [userBanned, setUserBanned] = useState(user.banned)
    const [userAdmin, setUserAdmin] = useState(user.roles.includes('ROLE_STAFF'))

    const renderActions = user.id != userId

    const toggleBan = () => {

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'user_id='+user.id
        }

        fetch(user_ban_route, requestOptions)
            .then(_ => setUserBanned(!userBanned))
    }

    const toggleAdmin = () => {
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'user_id='+user.id
        };

        fetch(user_admin_route, requestOptions)
            .then(_ => setUserAdmin(!userAdmin));
    }

    const user_profile_url = user_profile_route + '/' + user.id

    return <>
    <tr>
        <td className='text-center'><a href={user_profile_url}>{ user.firstName} { user.lastName }</a></td>
        <td className='text-center'>{ user.faculty.shortcut }</td>
        <td className='text-center'>{ user.email }</td>

        { (user.banned)
        ?    <td className='text-center'><i className="fa-solid fa-xmark"></i>{t('inactive')}</td>
        :    <td className='text-center'><i className="fa-solid fa-check"></i>{t('active')}</td>
        }

        <td className='text-center'>
            { userAdmin
            ? t('admin')
            : t('user')
            }
        </td>
        <td style={ {minWidth: "max-content"} } className='text-center'>
            { renderActions 
                && (
                    userBanned
                    ?
                <button onClick={toggleBan} type="submit" className="btn btn-warning">
                    {t('user_unblock')}
                </button>
                    : 
                <button onClick={toggleBan} type="submit" className="btn btn-danger">
                    {t('user_block')}
                </button>
                )
            }

            { renderActions && !userBanned && 
             (
                !userAdmin
                ?
                    <button onClick={toggleAdmin} type="submit" className="btn btn-dark">
                        {t('make_admin')}
                    </button>
                 :
                    <button onClick={toggleAdmin} type="submit" className="btn btn-secondary">
                        {t('unmake_admin')}
                    </button>
             )
            }
        </td>
    </tr>
    </>
}