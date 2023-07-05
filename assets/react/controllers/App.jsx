import { BrowserRouter, Route, Routes } from 'react-router-dom'
import UserTable from './UserTable'
import SeasonEdit from './SeasonEdit'
import SeasonManagement from './SeasonManagement'
import SubmissionUpload from './SubmissionUpload'
import Navbar from './Navbar'
import Home from './Home'
import Rules from "./Rules";
import Footer from "./Footer";
import Login from "./Login";
import useAuth, {AuthProvider} from "./useAuth";
import React from "react";
import Registration from "./Register";
import Profile from "./Profile";

import _ from '../i8n'

export default function App(props) {

    const { auth } = useAuth();

    return (
        <>
        <BrowserRouter>
            <AuthProvider>
            <div className="container">
            <Navbar/>
            <Routes>
                <Route path='/' element={<Home/>} />
                <Route path='/rules' element={<Rules/>} />

                <Route path='/user'>
                    <Route path='login' element={<Login/>}/>
                    <Route path='register' element={<Registration/>}/>
                    <Route path='profile/:userId?' element={<Profile/>}/>
                </Route>

                <Route path='/management'>
                    <Route path='users' element={<UserTable {...props} />} />
                    <Route path='seasons' element={<SeasonManagement/>}/>
                    <Route path='season/:seasonId' element={<SeasonEdit/>}/>
                </Route>
                <Route path='submission/create' element={<SubmissionUpload/>}/>
            </Routes>
            </div>
            <Footer/>
            </AuthProvider>
        </BrowserRouter>
        </>
    );
}
