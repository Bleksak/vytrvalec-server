import axios from "axios";

//TODO error handling 
//TODO response types
export const getUserData = async (id: string) => {
    if (!id) return null;

    return axios.get(`/api/user/profile/${id}`).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}

export const getUserSubmissions = async (page: number) => {
    return axios.get(`/api/submission/list/${page}`).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}


export const login = async (username: string, password: string) => {
    return axios.postForm('/api/user/login', { email: username, password: password, }).then(
        res => {
            console.log('res', res);
            return res.data
        },
        err => console.log('err', err)
    );
}

export const logout = async (): Promise<boolean> => {
    return axios.get('/api/user/logout').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}

export const registerUser = (data: any) => { //TODO
    return axios.post('/api/user/register', data).then(
        res => {
            console.log('res', res);
            return res.data;
        }, err => console.log('err', err)

    );
}

export const toggleBan = async (id: number) => {
    const requestOptions = {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `user_id=${id}`
    }

    return axios.post('/api/management/users/ban', requestOptions).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );

}

export const toggleAdmin = async (id: number) => {
    const requestOptions = {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `user_id=${id}`
    };

    return axios.post('/api/management/users/admin', requestOptions).then(
        res => {
            console.log('res', res);
            return res.data;
        }, err => console.log('err', err)
    );
}

export const getAllUsers = () => {
    return axios.get('/api/management/users').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}

export const isAuthenticated = () => {
    return axios.get('/api/user/profile').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}

export const getUserCount = async () => {
    return axios.get('/api/user/count').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}