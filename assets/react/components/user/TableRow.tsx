import React, { useState } from "react"
import { useTranslation } from "react-i18next"
import { User } from "../../types";
import { toggleAdmin, toggleBan } from "../../api/UserApi";

interface TRProps {
    user: User;
    userId: number;
}

const TableRow = ({ user, userId }: TRProps) => {
    const { t, i18n } = useTranslation()
    const [userBanned, setUserBanned] = useState<boolean>(user.banned)
    const [userAdmin, setUserAdmin] = useState<boolean>(user.roles.includes('ROLE_STAFF'))

    const renderActions = user.id !== userId

    const handleToggleBan = () => {
        toggleBan(user.id).then(() => setUserBanned(banned => !banned));
    }

    const handleToggleAdmin = () => {
        toggleAdmin(user.id).then(() => setUserAdmin(admin => !admin));
    }

    return (
        <tr>
            <td className='text-center'><a href={`/user/profile/${user.id}`}>{user.firstName} {user.lastName}</a></td>
            <td className='text-center'>{user.faculty.shortcut}</td>
            <td className='text-center'>{user.email}</td>

            {(user.banned)
                ? <td className='text-center'><i className="fa-solid fa-xmark"></i>{t('inactive')}</td>
                : <td className='text-center'><i className="fa-solid fa-check"></i>{t('active')}</td>
            }

            <td className='text-center'>
                {userAdmin
                    ? t('admin')
                    : t('user')
                }
            </td>
            <td style={{ minWidth: "max-content" }} className='text-center'>
                {renderActions
                    && (
                        userBanned
                            ?
                            <button onClick={handleToggleBan} type="submit" className="btn btn-warning">
                                {t('user_unblock')}
                            </button>
                            :
                            <button onClick={handleToggleBan} type="submit" className="btn btn-danger">
                                {t('user_block')}
                            </button>
                    )
                }

                {renderActions && !userBanned &&
                    (
                        !userAdmin
                            ?
                            <button onClick={handleToggleAdmin} type="submit" className="btn btn-dark">
                                {t('make_admin')}
                            </button>
                            :
                            <button onClick={handleToggleAdmin} type="submit" className="btn btn-secondary">
                                {t('unmake_admin')}
                            </button>
                    )
                }
            </td>
        </tr>
    )
}

export default TableRow;