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
mujer(raquel).
mujer(elena).
hombre(pedro).
hombre(juan).
hombre(jose).
hombre(miguel).
hombre(rodolfo).
progenitorhijo(X,W) :- progenitor(X,W), write('true').
madre(X,W) :- progenitor(X,W), mujer(X), write('true'), write('X='), write(X), write(','), write('W='), write(W), nl, fail.
padre(X,W) :- progenitor(X, W), hombre(X), write('true,'), write('X='), write(X), write(','), write('W='), write(W), nl, fail.
hermano(X,Y)  :- progenitor(W,X), progenitor(W,Y), X \= Y, hombre(X), write('true').
hermana(X,Y)  :- progenitor(W,X), progenitor(W,Y), X \= Y, mujer(X), write('true').
hermanos(X,Y) :- progenitor(W,X), progenitor(W,Y), X \= Y, write('true').
tio(X,W) :- hermanos(Y,X), progenitor(Y,W), hombre(X), write('true').
tia(X,W) :- hermanos(Y,X), progenitor(Y,W), mujer(X), write('true').