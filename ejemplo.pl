# Relaciones
progenitor(pedro,teresa).
progenitor(pedro,rodolfo).
progenitor(maria,teresa).
progenitor(maria,elena).
progenitor(teresa,jorge).
progenitor(teresa,raquel).
progenitor(elena,jose).
progenitor(juan,jose).
progenitor(raquel,miguel).
mujer(maria).
mujer(teresa).
mujer(maria).
mujer(raquel).
mujer(elena).
hombre(pedro).
hombre(juan).
hombre(jose).
hombre(miguel).
progenitorhijo(X,W) :- progenitor(X,W), write('true'), nl.
madre(X,W) :- progenitor(X,W), mujer(X), write('true'), nl.
padre(X,W) :- progenitor(X,W), hombre(X), write('true'), nl.
hermanos(X,Y) :- progenitor(W,X), progenitor(W,Y), X \= Y, write('true'), nl.
tio(X,W) :- hermanos(Y,X), progenitor(Y,W), hombre(X), write('true'), nl.
tia(X,W) :- hermanos(Y,X), progenitor(Y,W), mujer(X), write('true'), nl.