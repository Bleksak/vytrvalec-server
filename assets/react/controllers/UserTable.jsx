export default function UserTable({app, users, user_profile_route, user_ban_route, user_admin_route}) {

    console.log("Pavlovi smrdi koule")

    return <>
    <input type="text" id="search-bar" onkeyup="search()" placeholder="'search_name'|trans" />
    <table>
        <thead>
            <th>
                'name' trans
            </th>
            <th>
                'faculty' trans
            </th>
            <th>
                'email' trans
            </th>
            <th>
                'status' trans
            </th>
            <th>
                'access_right' trans
            </th>
            <th>
                'action' trans
            </th>
        </thead>
        <tbody>
        for(user of users) {
            <tr>
                <td><a href="{ user_profile_route }">{ user.firstname} { user.lastname }</a></td>
                <td>{ user.faculty.shortcut }</td>
                <td>{ user.email }</td>

                { (user.banned)
                ?    <td><i className="fa-solid fa-xmark"></i> 'inactive'|trans </td>
                :    <td><i className="fa-solid fa-check"></i> 'active'|trans </td>
                }

                <td>
                    { user.roles.contains("ROLE_STAFF") 
                    ? 'admin' | trans
                    : 'user' | trans
                    }
                </td>
                <td>
                    <div style="min-width: max-content">
                    <form className="small-form" action="{{ path('management_users_ban') }}" method="POST">
                    <input type="hidden" name="user_id" value="{{ user.id }}"/>
                        { user.id != app.user.id 
                            && (
                                user.banned 
                                ?
                            <button onclick="return confirm('{{ 'user_unblock_confirm'|trans }}');" type="submit" className="btn btn-warning">
                            'user_unblock'|trans
                            </button>
                                : 
                            <button onclick="return confirm('{{ 'user_block_confirm'|trans }}');" type="submit" className="btn btn-danger">
                            'user_block'|trans
                            </button>
                            )
                        }
                    </form>

                    { user.id != app.user.id && !user.banned && (
                        <form className="small-form" action="{user_admin_route}" method="POST">
                            <input type="hidden" name="user_id" value="{{ user.id }}"/>
                            { user.roles.contains('ROLE_STAFF') 
                            ?
                            <button onclick="return confirm('{{ 'make_admin_confirm'|trans }}');" type="submit" className="btn btn-dark">
                            'make_admin'|trans
                            </button>
                            :
                            <button onclick="return confirm('{{ 'unmake_admin_confirm'|trans }}');" type="submit" className="btn btn-secondary">
                            'unmake_admin'|trans
                            </button>
                            }
                        </form>
                        )
                    }
                    </div>
                </td>
            </tr>
        }
        </tbody>
    </table>
    </>
}