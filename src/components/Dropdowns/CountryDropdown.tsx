import React, { useState } from 'react';
import Select from 'react-select'

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL']

type TypeCountries = {
   value: string,
   label: string | ReactElement,
   flag: string,
}

const CountryDropdown = () => {
   const countries: Array<TypeCountries> = [
      { value: 'US', label: 'United States', flag: `${getPublicUrl}/images/helpii-user-settings/flags/bharat.svg` },
      { value: 'SO', label: 'Somalia', flag: `${getPublicUrl}/images/helpii-user-settings/flags/bharat.svg` }
   ]

   return (
      <div className='mb-10'>
         <label className='form-label'>Select a country</label>
         <Select className='react-select-styled' classNamePrefix='react-select'
         options={countries.map((item) => {
            item.label = (
               <div className='label'>
                  <img src={item.flag} alt='flag' className='w-20px rounded-circle me-2' />
                  <span>{item.label}</span>
               </div>
            )
            return item
         })}
         isSearchable={true}
         placeholder='Select a country'
         defaultValue={countries[0]}
         isClearable
         onChange={(selectedOption) => setFormData({ ...formData, country: selectedOption.value })}/>
      </div>
   );
};

export default CountryDropdown;
