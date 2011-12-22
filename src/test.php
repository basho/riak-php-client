<?php

namespace Riak;

/*
* Lightweight TAP test harness.
*/
class Test {

    # How many tests are planned
    protected static $planned = NULL;

    # How many tests we've run, if 'planned' is still null by the time we're
    # done we report the total count at the end
    protected static $run = 0;

    # Are are we currently within todo_start()/todo_end() ?
    protected static $todo = array();

    public static function plan($plan, $why = '')
    {
    
        self::$planned = true;
    
        switch ($plan)
        {
          case 'no_plan':
            self::$planned = false;
            break;
          case 'skip_all';
            printf("1..0%s\n", $why ? " # Skip $why" : '');
            exit;
          default:
            printf("1..%d\n", $plan);
            break;
        }
    }
    
    public static function pass($desc = '')
    {
        return self::_proclaim(true, $desc);
    }
    
    public static function fail($desc = '')
    {
        return self::_proclaim(false, $desc);
    }
    
    public static function ok($cond, $desc = '') {
        return self::_proclaim($cond, $desc);
    }
    
    public static function debug( $var, $comment = NULL ){
       if( $comment ) echo "#  " . $comment . "\n# ----\n";
       echo "#  " . str_replace("\n", "\n#  ",self::stringify( $var) ) . "\n#\n";
    }
    
    public static function is($have, $want, $desc = '') {
        $pass = $have == $want;
        return self::_proclaim($pass, $desc, /* todo */ false, $have, $want);
    }
    
    public static function isa($have, $want, $desc = '') {
        $pass = ( $have instanceof $want );
        return self::_proclaim($pass, $desc, /* todo */ false, $have, $want);
    }
    
    public static function isnt($have, $want, $desc = '') {
        $pass = $have != $want;
        return self::_proclaim($pass, $desc, /* todo */ false, $have, $want, /* negated */ true);
    }
    
    public static function like($have, $want, $desc = '') {
        $pass = preg_match($want, $have);
        return self::_proclaim($pass, $desc, /* todo */ false, $have, $want);
    }
    
    public static function unlike($have, $want, $desc = '') {
        $pass = !preg_match($want, $have);
        return self::_proclaim($pass, $desc, /* todo */ false, $have, $want, /* negated */ true);
    }
    
    public static function cmp_ok($have, $op, $want, $desc = '')
    {
        $pass = null;
    
        # See http://www.php.net/manual/en/language.operators.comparison.php
        switch ($op)
        {
          case '==':
            $pass = $have == $want;
            break;
          case '===':
            $pass = $have === $want;
            break;
          case '!=':
          case '<>':
            $pass = $have != $want;
            break;
          case '!==':
            $pass = $have !== $want;
            break;
          case '<':
            $pass = $have < $want;
            break;
          case '>':
            $pass = $have > $want;
            break;
          case '<=':
            $pass = $have <= $want;
            break;
          case '>=':
            $pass = $have >= $want;
            break;
        default:
            if (function_exists($op)) {
                $pass = $op($have, $want);
            } else {
                die("No such operator or function $op\n");
            }
        }
        $have = self::stringify( $have );
        $want = self::stringify( $want );
        return self::_proclaim($pass, $desc, /* todo */ false, $have, "$op $want");
    }
    
    public static function diag($message)
    {
        if (is_array($message))
        {
            $message = implode("\n", $message);
        }
    
        foreach (explode("\n", $message) as $line)
        {
            echo "# $line\n";
        }
    }

    public static function todo_start($why = '')
    {    
        self::$todo[] = $why;
    }
    
    public static function todo_end()
    {    
        if (count(self::$todo) == 0) {
            die("todo_end() called without a matching todo_start() call");
        } else {
            array_pop(self::$todo);
        }
    }
    
    #
    # The code below consists of private utility functions for the above functions
    #
    
    protected static function _proclaim(
        $cond, # bool
        $desc = '',
        $todo = false,
        $have = null,
        $want = null,
        $negate = false) {
        
        self::$run += 1;
    
        # We're in a TODO block via todo_start()/todo_end(). TODO via specific
        # functions is currently unimplemented and will probably stay that way
        if (count(self::$todo)) {
            $todo = true;
        }
    
        # Everything after the first # is special, so escape user-supplied messages
        $desc = str_replace('#', '\\#', $desc);
        $desc = str_replace("\n", '\\n', $desc);
    
        $ok = $cond ? "ok" : "not ok";
        $directive = '';
    
        if ($todo) {
            $todo_idx = count(self::$todo) - 1;
            $directive .= ' # TODO ' . self::$todo[$todo_idx];
        }
    
        printf("%s %d %s%s\n", $ok, self::$run, $desc, $directive);
    
        # report a failure
        if (!$cond) {
            # Every public function in this file calls _proclaim so our culprit is
            # the second item in the stack
            $caller = debug_backtrace();
            $call = $caller['1'];
        
            if (($have != null) || ($want != null)) {
              $have = self::stringify($have);
              $want = self::stringify($want);
              self::diag(
                  sprintf(" Failed%stest '%s'\n in %s at line %d\n have: %s\n want: %s",
                      $todo ? ' TODO ' : ' ',
                      $desc,
                      $call['file'],
                      $call['line'],
                      $have,
                      $want
                  )
              );
            } else {
              self::diag(
                  sprintf(" Failed%stest '%s'\n in %s at line %d",
                      $todo ? ' TODO ' : ' ',
                      $desc,
                      $call['file'],
                      $call['line']
                  )
              );
            }
        }
    
        return $cond;
    }
    
    public static function _ends()
    {    
        if (count(self::$todo) != 0) {
            $todos = join("', '", self::$todo);
            die("Missing todo_end() for '$todos'");
        }
    
        if (!self::$planned) {
            printf("1..%d\n", self::$run);
        }
    }

    public static function stringify( $var ){
        if( is_bool( $var ) ) return '(bool) ' . ($var ? 'true' : 'false');
        if( is_scalar( $var ) ) return $var;
        if( is_object( $var ) && method_exists($var, '__toString') ) return $var->__toString();
        return print_r( $var, TRUE);
    }
}

register_shutdown_function(array('Riak\Test', '_ends'));

$prev = NULL;

$prev = set_exception_handler( function ( Exception $e ) use ( & $prev  ) {
        print "\nUncaught " . $e->__toString();
        if( $prev != null ) call_user_func( $prev, $e );
    }
);
