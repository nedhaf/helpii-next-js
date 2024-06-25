// context.js
import React, { createContext, useContext, useState } from 'react';

const AppContext = createContext();

export function AppWrapper({ children }) {
  const [hiddenData, setHiddenData] = useState('');

  return (
    <AppContext.Provider value={{ hiddenData, setHiddenData }}>
      {children}
    </AppContext.Provider>
  );
}

export function useAppContext() {
  return useContext(AppContext);
}
