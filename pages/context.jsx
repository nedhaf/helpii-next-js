import React from 'react';

const MyContext = React.createContext(null);

export function MyContextProvider({ children }) {
  const [state, setState] = React.useState({
    user: null,
  });

  return (
    <MyContext.Provider value={state}>
      {children}
    </MyContext.Provider>
  );
}

export default MyContext;