import React from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import _ from '../i8n'
import UserTable from './UserTable'
import SeasonEdit from './SeasonEdit'
import SeasonManagement from './SeasonManagement'

export default function App(props) {
    return (
        <BrowserRouter>
            <Routes>
                <Route path='management'>
                    <Route path='users' element={<UserTable {...props} />} />
                    <Route path='seasons' element={<SeasonManagement/>}/>
                    <Route path='season/:seasonId' element={<SeasonEdit/>}/>
                </Route>
            </Routes>
        </BrowserRouter>
    )
}