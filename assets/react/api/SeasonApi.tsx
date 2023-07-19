import Charity from "../types/Charity";
import axios from "axios";

export const editCharity = async (charity: Charity) => {
    const requestOptions = {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(charity.id!) + '&charityName=' + encodeURIComponent(charity.name) + '&charityDescription=' + encodeURIComponent(charity.description)
    };

    return axios.post('/api/management/charity/edit', requestOptions).then(
        res => {
            console.log('res', res);
            return res.data
        },
        err => console.log('err', err)
    );
}

export const getSeasonData = async (id: string | undefined) => {
    return axios.get(`/api/season/${id}`).then(
        res => {
            console.log('res', res);
            return res.data
        },
        err => console.log('err', err)
    );
}

export const getRunningSeason = async () => {
    return axios.get('/api/season/running').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}

export const getAllSeasons = async () => {
    return axios.get('/api/season/list').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}

export const createNewSeason = (date: string, name: string, description: string) => {
    const requestOptions = {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'beginDate=' + date + "&charityName=" + name + "&charityDescription=" + description
    };

    return axios.post('/api/management/season/new', requestOptions).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}
