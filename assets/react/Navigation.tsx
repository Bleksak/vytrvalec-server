import { Route, Routes } from 'react-router-dom'
import React from "react";
import { Home, Login, Profile, Registration, Rules, SeasonEdit, SeasonManagement, SubmissionUpload } from './screens';
import UserTable from './components/UserTable';

const Navigation = (props: any) => (
    <Routes>
        <Route path='/' element={<Home />} />
        <Route path='/rules' element={<Rules />} />

        <Route path='/user'>
            <Route path='login' element={<Login />} />
            <Route path='register' element={<Registration />} />
            <Route path='profile/:userId?' element={<Profile />} />
        </Route>

        <Route path='/management'>
            <Route path='users' element={<UserTable {...props} />} />
            <Route path='seasons' element={<SeasonManagement />} />
            <Route path='season/:seasonId' element={<SeasonEdit />} />
        </Route>
        <Route path='submission/create' element={<SubmissionUpload />} />
    </Routes>
);

export default Navigation;